<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

class SagaPass extends ProxyLoadingCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $sagas = array();
        foreach ($container->findTaggedServiceIds('lite_cqrs.saga') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $definition->addTag('lite_cqrs.event_handler');
            $container->setDefinition($id, $definition);

            $sagas[$definition->getClass()] = new Reference($id);
        }

        $sagaProxyFactory = $container->findDefinition('litecqrs.saga.message_handler_factory');
        $sagaProxyFactory->addMethodCall('registerSagas', $sagas);
    }
}