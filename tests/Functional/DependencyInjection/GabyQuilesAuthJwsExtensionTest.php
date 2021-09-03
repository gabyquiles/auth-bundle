<?php

namespace GabyQuiles\Auth\Test\Functional\DependencyInjection;


use GabyQuiles\Auth\DependencyInjection\GabyQuilesAuthJwsExtension;
use GabyQuiles\Auth\Providers\AwsJwsProvider;
use Matthias\SymfonyDependencyInjectionTest\PhpUnit\AbstractExtensionTestCase;

class GabyQuilesAuthJwsExtensionTest extends AbstractExtensionTestCase
{
    protected function getContainerExtensions(): array
    {
        return array(
            new GabyQuilesAuthJwsExtension()
        );
    }

    /**
     * @test
     */
    public function after_loading_the_correct_parameter_has_been_set()
    {
        $this->load([
            'token_ttl' => 3600,
            'clock_skew' => 50,
            'pool_id' => 'PoolId',
            'region' => 'us-east-1'
        ]);

        $this->assertContainerBuilderHasParameter('gaby_quiles_auth_jws.token_ttl', '3600');
        $this->assertContainerBuilderHasService('gaby_quiles_auth_jws.aws_jwt_provider', AwsJwsProvider::class);
    }
}