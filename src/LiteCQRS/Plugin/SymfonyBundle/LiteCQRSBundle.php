<?php

namespace LiteCQRS\Plugin\SymfonyBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;

use LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler\EventPass;
use LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler\CommandPass;
use LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler\SagaPass;

class LiteCQRSBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CommandPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new SagaPass(), PassConfig::TYPE_AFTER_REMOVING);
        $container->addCompilerPass(new EventPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}

