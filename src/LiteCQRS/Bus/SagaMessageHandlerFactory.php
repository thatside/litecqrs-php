<?php

namespace LiteCQRS\Bus;

use LiteCQRS\Saga\Metadata\MetadataFactoryInterface;
use LiteCQRS\Saga\State\Manager\StateManagerInterface;
use LiteCQRS\Saga\State\StateRepositoryInterface;

class SagaMessageHandlerFactory implements ProxyFactoryInterface
{
    private $stateManager;
    private $metadataFactory;
    private $eventBus;
    private $commandBus;
    private $sagas;

    public function __construct(
        StateManagerInterface $stateManager,
        MetadataFactoryInterface $metadataFactory,
        EventMessageBus $eventBus,
        CommandBus $commandBus,
        array $sagas
    ) {
        $this->stateManager = $stateManager;
        $this->metadataFactory = $metadataFactory;
        $this->eventBus = $eventBus;
        $this->commandBus = $commandBus;
        $this->sagas = $sagas;
    }

    public function __invoke($handler) : MessageHandlerInterface
    {
        if (method_exists($handler, 'isSaga') && $handler->isSaga()) {
            return new SagaMessageHandler($handler, $this->stateManager, $this->metadataFactory, $this->eventBus, $this->commandBus, $this->sagas);
        } else {
            return $handler;
        }
    }
}