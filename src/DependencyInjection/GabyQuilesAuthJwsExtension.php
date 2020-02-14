<?php

namespace GabyQuiles\Auth\DependencyInjection;


use GabyQuiles\Auth\Loaders\JwkFetcher;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class GabyQuilesAuthJwsExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
//        TODO: Add check for class
        $loader->load('services.yaml');
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);
        $container->setParameter('gaby_quiles_auth_jws.token_ttl', $config['token_ttl']);
        $container->setParameter('gaby_quiles_auth_jws.clock_skew', $config['clock_skew']);
        $container->setParameter('gaby_quiles_auth_jws.pool_id', $config['pool_id']);
        $container->setParameter('gaby_quiles_auth_jws.region', $config['region']);
    }

    public function process(ContainerBuilder $container)
    {
        /** @var JwkFetcher $jwkFetcher */
        $jwkFetcher = $container->get('gaby_quiles_auth_jws.jwk_fetcher');
        $jwkFetcher->getJwk();
    }
}