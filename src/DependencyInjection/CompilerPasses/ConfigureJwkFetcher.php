<?php

namespace GabyQuiles\Auth\DependencyInjection\CompilerPasses;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureJwkFetcher implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $fetcherDefinition = $container->getDefinition('gaby_quiles_auth_jws.jwk_fetcher');
        $fetcherDefinition->setArgument(0, $container->getParameter('gaby_quiles_auth_jws.pool_id'));
        $fetcherDefinition->setArgument(1, $container->getParameter('gaby_quiles_auth_jws.region'));
    }
}