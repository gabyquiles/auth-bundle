<?php

namespace GabyQuiles\Auth\Test\Providers;

use CoderCat\JWKToPEM\JWKConverter;
use GabyQuiles\Auth\Loaders\JwkFetcher;
use GabyQuiles\Auth\Loaders\JwkKeyLoader;
use GabyQuiles\Auth\Providers\AwsJwsProvider;
use GabyQuiles\Auth\Signer\SignerFactory;
use Lcobucci\JWT\Signer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class AwsJwsProviderTest extends TestCase
{
    private $sut;

    public function setUp(): void
    {
        $mockedSigner = $this->createConfiguredMock(Signer::class, [
            'verify' => true,
            'getAlgorithmId' => 'RS256'
        ]);
        $mockedSignerFactory = $this->createConfiguredMock(SignerFactory::class, [
            'getSignerForAlgorithm' => $mockedSigner
        ]);
        $cache = new FilesystemAdapter();
        $cache->clear();
        $mockedFetcher = $this->createConfiguredMock(JwkFetcher::class, [
            'getJwk'=>['keys' => [['kid' => 'KIDfromAWS']]]
        ]);

        $mockedConverter = $this->createConfiguredMock(JWKConverter::class, [
            'toPEM' => 'ThisIsMySuperSecretSignature'
        ]);
        $mockedJwkLoader = new JwkKeyLoader($cache, $mockedFetcher, $mockedConverter);

        $this->sut = new AwsJwsProvider($mockedSignerFactory, $mockedJwkLoader, 3601, 100);
    }

    private function generateNewToken($issuer = '', $subject='', $issuedAt='', $expiration='', $jwtId='') {
        $header = '{"kid":"KIDfromAWS","alg":"RS256"}';
        $claims = [
            'sub',
            'iss',
            'exp',
            'iat',
            'jti',
        ];
        $claimsPayload = [
            'sub' => $subject,
            'iss' => $issuer,
            'exp' => $expiration,
            'iat' => $issuedAt,
            'jti' => $jwtId,
        ];
        foreach ($claims as $claim) {
            if($claimsPayload[$claim] === null) {
                unset($claimsPayload[$claim]);
            }
        }

        $signature = 'ThisIsMySuperSecretSignature';
        return base64_encode($header).'.'.base64_encode(json_encode($claimsPayload)).'.'.base64_encode($signature);
    }

    public function testLoadingValidToken()
    {
        $jwt = $this->generateNewToken('', '', time(), time() - 50, '');
        $token = $this->sut->load($jwt);
        $this->assertFalse($token->isExpired(), "Token is expired");
        $this->assertTrue($token->isVerified(), "Token not verified");
        $this->assertFalse($token->isInvalid(), "Invalid token");
    }

    public function testLoadingExpiredToken()
    {
        $jwt = $this->generateNewToken('', '', time(), time() - 100, '');
        $token = $this->sut->load($jwt);
        $this->assertTrue($token->isExpired(), "Token not expired");
    }

    /**
     * @dataProvider providesInvalidTokens
     */
    public function testLoadingInvalidToken($jwt)
    {

        $token = $this->sut->load($jwt);
        $this->assertTrue($token->isInvalid(), "Invalid token");
    }

    public function providesInvalidTokens()
    {
        $futureDate = time() + 100;
        return [
            "Token Issued in the future" => [$this->generateNewToken('', '', time() + 500, $futureDate, '')],
            "Token with expiration Date as string" => [$this->generateNewToken('', '', time() - 500, null, '')],
        ];
    }
}