<?php

namespace LiteCQRS\Bus;

use LiteCQRS\Saga\Event\SagaDoneEvent;
use LiteCQRS\Saga\Event\SagaPostHandleEvent;
use LiteCQRS\Saga\Event\SagaPreHandleEvent;
use LiteCQRS\Saga\Metadata\MetadataFactoryInterface;
use LiteCQRS\Saga\SagaInterface;
use LiteCQRS\Saga\State\Manager\StateManagerInterface;

class SagaMessageHandler implements MessageHandlerInterface
{
    private $next;
    private $stateManager;
    private $metadataFactory;
    private $eventBus;
    private $commandBus;
    private $sagas;

    public function __construct(
        MessageHandlerInterface $next,
        StateManagerInterface $stateManager,
        MetadataFactoryInterface $metadataFactory,
        EventMessageBus $eventMessageBus,
        CommandBus $commandBus,
        array $sagas)
    {
        $this->next = $next;
        $this->stateManager = $stateManager;
        $this->metadataFactory = $metadataFactory;
        $this->eventBus = $eventMessageBus;
        $this->commandBus = $commandBus;
        $this->sagas = $sagas;
    }

    public function handle($event)
    {
        /**
         * @var string $sagaClass
         * @var SagaInterface $saga
         */
        foreach ($this->sagas as $sagaClass => $saga) {
            $metadata = $this->metadataFactory->create($saga);

            if (! $metadata->handles($event)) {
                continue;
            }

            $state = $this->stateManager->get($metadata->criteria($event), $sagaClass);

            if (null === $state) {
                continue;
            }
            $this->eventBus->dispatch(SagaPreHandleEvent::create($sagaClass, $state->getId()));

            $newState = $saga->handle($event, $state);

            $this->eventBus->dispatch(SagaPostHandleEvent::create($sagaClass, $state->getId()));

            $this->stateManager->save($newState, $sagaClass);

            $this->commandBus->handleAll();

            if ($newState->isDone()) {
                $this->eventBus->dispatch(SagaDoneEvent::create($sagaClass, $state->getId()));
            }
        }
    }

}