<?php

namespace GabyQuiles\Auth\Providers;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use Lexik\Bundle\JWTAuthenticationBundle\Signature\LoadedJWS;

class TestProvider implements JWSProviderInterface
{
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
        // This is a stub provider used to ease testing. Does not sign any token.
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
        $payload = json_decode(base64_decode($token), true);
        $payload['iat'] = time();
        return new LoadedJWS($payload, true, false);
    }
}