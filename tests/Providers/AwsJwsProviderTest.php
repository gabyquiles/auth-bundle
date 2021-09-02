<?php

namespace GabyQuiles\Auth\Test\Providers;

use GabyQuiles\Auth\Loaders\JwkKeyLoader;
use GabyQuiles\Auth\Providers\AwsJwsProvider;
use GabyQuiles\Auth\Signer\SignerFactory;
use Lcobucci\JWT\Signer;
use PHPUnit\Framework\TestCase;

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

        $mockedJwkLoader = $this->createMock(JwkKeyLoader::class);

        $this->sut = new AwsJwsProvider($mockedSignerFactory, $mockedJwkLoader, 3601, 100);
    }

    private function generateNewToken($issuer = '', $subject='', $issuedAt='', $expiration='', $jwtId='') {
        $header = '{"kid":"KID from AWS","alg":"RS256"}';
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