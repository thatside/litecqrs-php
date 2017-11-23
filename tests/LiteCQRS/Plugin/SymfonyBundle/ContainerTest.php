<?php

namespace LiteCQRS\Plugin\SymfonyBundle;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\Compiler\ResolveDefinitionTemplatesPass;

use LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\LiteCQRSExtension;
use LiteCQRS\Plugin\SymfonyBundle\DependencyInjection\Compiler\EventPass;

class ContainerTest extends TestCase
{
    public function testContainer()
    {
        $container = $this->createTestContainer();

        $this->assertInstanceOf('LiteCQRS\Bus\CommandBus',      $container->get('command_bus'));
        $this->assertInstanceOf('LiteCQRS\Bus\EventMessageBus', $container->get('litecqrs.event_message_bus'));
        $this->assertInstanceof('LiteCQRS\Plugin\Doctrine\ORMRepository', $container->get('litecqrs.repository'));
        $this->assertInstanceOf('LiteCQRS\EventStore\SerializerInterface', $container->get('litecqrs.serializer'));
        $this->assertInstanceOf('LiteCQRS\Plugin\SymfonyBundle\Controller\CRUDHelper', $container->get('litecqrs.crud.helper'));
    }

    public function createTestContainer()
    {
        $container = new ContainerBuilder(new ParameterBag(array(
            'kernel.debug' => false,
            'kernel.bundles' => array(),
            'kernel.cache_dir' => sys_get_temp_dir(),
            'kernel.environment' => 'test',
            'kernel.root_dir' => __DIR__.'/../../../../' // src dir
        )));
        $loader = new LiteCQRSExtension();
        $container->registerExtension($loader);
        $container->set('doctrine.orm.default_entity_manager', $this->getMockClass('Doctrine\ORM\EntityManager', array(), array(), '', false));
        $container->set('logger', $this->createMock('Monolog\Logger'));
        $loader->load(array(array(
            "orm"              => true,
            "monolog"              => true,
        )), $container);

        $container->getCompilerPassConfig()->setAfterRemovingPasses(array(new EventPass()));
        $container->getCompilerPassConfig()->setRemovingPasses(array());
        $container->compile();

        return $container;
    }
}

