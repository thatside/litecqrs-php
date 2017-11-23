<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;

class EventPass extends ProxyLoadingCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $services = array();
        foreach ($container->findTaggedServiceIds('lite_cqrs.event_handler') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $class = $definition->getClass();

            $reflClass = new \ReflectionClass($class);
            foreach ($reflClass->getMethods() as $method) {
                if ($method->getNumberOfParameters() != 1) {
                    continue;
                }

                $methodName = $method->getName();
                if (strpos($methodName, "on") !== 0) {
                    continue;
                }

                $eventName = strtolower(substr($methodName, 2));

                if (!isset($services[$eventName])) {
                    $services[$eventName] = array();
                }

                $services[$eventName][] = $id;
            }
        }

        $messageBus = $container->findDefinition('litecqrs.event_message_bus');
        $args = $messageBus->getArguments();
        $args[1] = $this->getProxyFactories($container, 'litecqrs.event.proxy_factory');
        $messageBus->setArguments($args);
        $messageBus->addMethodCall('registerServices', array($services));
    }
}

