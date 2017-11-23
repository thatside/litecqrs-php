<?php

namespace LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

abstract class ProxyLoadingCompilerPass implements CompilerPassInterface
{
    protected function getProxyFactories(ContainerBuilder $container, $tag)
    {
        $services = array();
        foreach ($container->findTaggedServiceIds($tag) as $id => $attributes) {
            if (!isset($attributes['priority'])) {
                $attributes['priority'] = 0;
            }
            if (!isset($services[$attributes['priority']])) {
                $services[$attributes['priority']] = array();
            }
            $services[$attributes['priority']][] = new Reference($id);
        }

        $flat = array();
        foreach (array_reverse($services) as $s) {
            foreach ($s as $service) {
                $flat[] = $service;
            }
        }

        return $flat;
    }
}