<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

class CommandPass extends ProxyLoadingCompilerPass
{
    public function process(ContainerBuilder $container)
    {
        $bus = $container->findDefinition('command_bus');
        $args       = $bus->getArguments();
        $args[1]    = $this->getProxyFactories($container, 'lite_cqrs.command_proxy_factory');
        $bus->setArguments($args);

        $services = array();
        foreach ($container->findTaggedServiceIds('lite_cqrs.command_handler') as $id => $attributes) {
            $definition = $container->findDefinition($id);
            $class = $definition->getClass();

            $reflClass = new \ReflectionClass($class);

            foreach ($reflClass->getMethods(\ReflectionMethod::IS_PUBLIC) as $method) {
                // skip events
                if (strpos($method->getName(), "on") === 0) {
                    continue;
                }

                if ($method->getNumberOfParameters() != 1) {
                    continue;
                }

                $commandParam = current($method->getParameters());

                if (!$commandParam->getClass()) {
                    continue;
                }

                $commandClass = $commandParam->getClass();
                $commandName = strtolower(str_replace("Command", "", $commandClass->getShortName()));

                // skip methods where the command class name does not match the method name
                if ($commandName !== strtolower($method->getName())) {
                    continue;
                }

                $services[$commandClass->getName()] = $id;
            }
        }

        $bus->addMethodCall('registerServices', array($services));
    }
}