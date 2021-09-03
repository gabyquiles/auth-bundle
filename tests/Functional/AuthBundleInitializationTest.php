<?php

namespace GabyQuiles\Auth\Test\Functional;

use GabyQuiles\Auth\GabyQuilesAuthJwsBundle;
use GabyQuiles\Auth\Providers\AwsJwsProvider;
use Lexik\Bundle\JWTAuthenticationBundle\LexikJWTAuthenticationBundle;
use Nyholm\BundleTest\BaseBundleTestCase;
use Nyholm\BundleTest\CompilerPass\PublicServicePass;
use Symfony\Bundle\SecurityBundle\SecurityBundle;

class AuthBundleInitializationTest extends BaseBundleTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Make services public that have an idea that matches a regex
        $this->addCompilerPass(new PublicServicePass('|gaby_quiles_auth_jws.*|'));
    }


    protected function getBundleClass()
    {
        return GabyQuilesAuthJwsBundle::class;
    }

    public function testInitBundle()
    {
        // Create a new Kernel
        $kernel = $this->createKernel();

        $kernel->addConfigFile(__DIR__.'/config.yml');

        // Add some other bundles we depend on
        $kernel->addBundle(SecurityBundle::class);
        $kernel->addBundle(LexikJWTAuthenticationBundle::class);

        // Boot the kernel.
        $this->bootKernel();

        // Get the container
        $container = $this->getContainer();
        // Test if you services exists
        $this->assertTrue($container->has('gaby_quiles_auth_jws.aws_jwt_provider'));
        $service = $container->get('gaby_quiles_auth_jws.aws_jwt_provider');
        $this->assertInstanceOf(AwsJwsProvider::class, $service);
    }
}