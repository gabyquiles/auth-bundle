<?php

namespace GabyQuiles\Auth;

use GabyQuiles\Auth\DependencyInjection\ConfigureLcobucciEncoderCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class GabyQuilesAuthJwsBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ConfigureLcobucciEncoderCompilerPass());
    }
}