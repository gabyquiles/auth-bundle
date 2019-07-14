<?php

namespace GabyQuiles\Auth\Test\Stubs;


use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;

//use GabyQuiles\Auth\Providers\AwsJwsProvider;

class Autowired
{
    private $jwsProvider;

//    public function __construct(AwsJwsProvider $jwsProvider)
    public function __construct(JWSProviderInterface $jwsProvider)
    {
        $this->jwsProvider = $jwsProvider;
    }

    public function getJWSProvider()
    {
        return $this->jwsProvider;
    }
}