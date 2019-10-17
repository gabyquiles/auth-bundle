<?php

namespace GabyQuiles\Auth\DependencyInjection\CompilerPasses;


use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class ConfigureLcobucciEncoderCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $providerDefinition = $container->getDefinition('gaby_quiles_auth_jws.jwt_provider');

        $definition = $container->getDefinition('lexik_jwt_authentication.encoder.lcobucci');
        $definition->replaceArgument(0, $providerDefinition);
    }
}