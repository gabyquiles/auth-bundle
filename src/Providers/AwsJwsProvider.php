<?php

namespace GabyQuiles\Auth\Providers;


use GabyQuiles\Auth\Loaders\JwkKeyLoader;
use GabyQuiles\Auth\Signer\SignerFactory;
use Lcobucci\Clock\SystemClock;
use Lcobucci\JWT\Encoding\JoseEncoder;
use Lcobucci\JWT\Encoding\MicrosecondBasedDateConversion;
use Lcobucci\JWT\Parser;
use Lcobucci\JWT\Signer;
use Lcobucci\JWT\Signer\Key;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Token;
use Lcobucci\JWT\Token\Parser as JWTParser;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\JWT\Validation\Validator;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Services\KeyLoader\RawKeyLoader;
use Lexik\Bundle\JWTAuthenticationBundle\Signature\LoadedJWS;

//TODO: Move to its own bundle
class AwsJwsProvider implements JWSProviderInterface
{

    /**
     * @var RawKeyLoader
     */
    private $keyLoader;

    /**
     * @var Signer
     */
    private $signer;

    /**
     * @var int
     */
    private $ttl;

    /**
     * @var int
     */
    private $clockSkew;

    /**
     * @var SignerFactory
     */
    private $signerFactory;

    /**
     * @param RawKeyLoader $keyLoader
     * @param string $cryptoEngine
     * @param string $signatureAlgorithm
     * @param int|null $ttl
     * @param int $clockSkew
     *
     * @throws \InvalidArgumentException If the given crypto engine is not supported
     */
    public function __construct(SignerFactory $factory, JwkKeyLoader $keyLoader, $ttl, $clockSkew)
    {

        if (null !== $ttl && !is_numeric($ttl)) {
            throw new \InvalidArgumentException(sprintf('The TTL should be a numeric value, got %s instead.', $ttl));
        }

        if (null !== $clockSkew && !is_numeric($clockSkew)) {
            throw new \InvalidArgumentException(sprintf('The clock skew should be a numeric value, got %s instead.', $clockSkew));
        }

        $this->keyLoader = $keyLoader;
        $this->ttl = $ttl;
        $this->clockSkew = $clockSkew;
        $this->signerFactory = $factory;
    }

    /**
     * Creates a new JWS signature from a given payload.
     *
     * @param array $payload
     * @param array $header
     *
     * @return \Lexik\Bundle\JWTAuthenticationBundle\Signature\CreatedJWS
     */
    public function create(array $payload, array $header = [])
    {
        // This is provider do not sign any request. JWT is sign by AWS and this provider is used to decode it.
        throw new \BadMethodCallException('This provider does not sign any request');
    }

    /**
     * Loads an existing JWS signature from a given JWT token.
     *
     * @param string $token
     *
     * @return \Lexik\Bundle\JWTAuthenticationBundle\Signature\LoadedJWS
     */
    public function load($token)
    {
        if (class_exists(JWTParser::class)) {
            $jws = (new JWTParser(new JoseEncoder()))->parse((string) $token);
        } else {
            $jws = (new Parser())->parse((string) $token);
        }

        $payload = [];
        foreach ($jws->getClaims() as $claim) {
            $payload[$claim->getName()] = $claim->getValue();
        }

        return new LoadedJWS($payload, $this->verify($jws), null !== $this->ttl, $jws->getHeaders(), $this->clockSkew);
    }


    private function verify(Token $jwt)
    {
        $headers = $jwt->headers();
        $alg = $headers->get('alg');
        $kid = $headers->get('kid');
        $this->signer = $this->signerFactory->getSignerForAlgorithm($alg);
        if (class_exists(InMemory::class)) {
            $key = InMemory::plainText($this->keyLoader->loadKey($kid));
        } else {
            $key = new Key($this->keyLoader->loadKey($kid));
        }

        $clock = SystemClock::fromUTC();
        $validator = new Validator();

        return $validator->validate(
            $jwt,
            new ValidAt($clock, new \DateInterval("PT{$this->clockSkew}S")),
            new SignedWith($this->signer, $key)
        );
    }
}