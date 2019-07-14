<?php

namespace GabyQuiles\Auth\Test\DependencyInjection;


use GabyQuiles\Auth\DependencyInjection\GabyQuilesAuthJwsExtension;
use GabyQuiles\Auth\GabyQuilesAuthJwsBundle;
use GabyQuiles\Auth\Providers\AwsJwsProvider;
use GabyQuiles\Auth\Test\Stubs\Autowired;
use Lexik\Bundle\JWTAuthenticationBundle\DependencyInjection\LexikJWTAuthenticationExtension;
use Lexik\Bundle\JWTAuthenticationBundle\Services\JWSProvider\JWSProviderInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\DependencyInjection\FrameworkExtension;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;

class AutowiringTest extends TestCase
{
    public function testAutowiring()
    {
        $container = self::createContainerBuilder([
            'framework' => ['secret' => 'test'],
            'gaby_quiles_auth_jws' => [
                'token_ttl' => 3601,
                'clock_skew' => 1
            ]
        ]);
        $container
            ->register('autowired', Autowired::class)
            ->setPublic(true)
            ->setAutowired(true);
        $container->setAlias(JWSProviderInterface::class, 'gabyquiles_jwt_auth_extensions.aws_jwt_provider');
        
        $container->compile();
        /** @var Autowired $autowired */
        $autowired = $container->get('autowired');

        $this->assertInstanceOf(AwsJwsProvider::class, $autowired->getJWSProvider());
    }

    private static function createContainerBuilder(array $configs = [])
    {
        $container = new ContainerBuilder(new ParameterBag([
            'kernel.bundles' => ['FrameworkBundle' => FrameworkBundle::class, 'GabyQuilesAuthJwsBundle' => GabyQuilesAuthJwsBundle::class],
            'kernel.bundles_metadata' => [],
            'kernel.cache_dir' => __DIR__,
            'kernel.debug' => false,
            'kernel.environment' => 'test',
            'kernel.name' => 'kernel',
            'kernel.root_dir' => __DIR__,
            'kernel.project_dir' => __DIR__,
            'kernel.container_class' => 'AutowiringTestContainer',
            'kernel.charset' => 'utf8',
        ]));
        $container->registerExtension(new SecurityExtension());
        $container->registerExtension(new FrameworkExtension());
        $container->registerExtension(new LexikJWTAuthenticationExtension());
        $container->registerExtension(new GabyQuilesAuthJwsExtension());
        foreach ($configs as $extension => $config) {
            $container->loadFromExtension($extension, $config);
        }
        return $container;
    }
}