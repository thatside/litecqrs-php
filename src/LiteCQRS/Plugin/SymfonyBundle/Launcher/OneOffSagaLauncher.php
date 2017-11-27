<?php

namespace LiteCQRS\Plugin\SymfonyBundle\Launcher;

use LiteCQRS\Bus\EventMessageBus;
use LiteCQRS\DefaultDomainEvent;
use LiteCQRS\Saga\Event\SagaDoneEvent;
use LiteCQRS\Saga\State\State;

/**
 * Class OneOffSagaLauncher
 * Launches an event and listens to sagaDone event to capture saga end state
 */
class OneOffSagaLauncher
{
    private $endState;
    private $eventBus;

    public function __construct(EventMessageBus $eventBus)
    {
        $this->eventBus = $eventBus;
    }

    /**
     * Launch an event
     * @param DefaultDomainEvent $event
     */
    public function launch(DefaultDomainEvent $event)
    {
        $this->eventBus->dispatch($event);
    }

    /**
     * Receive and store saga end state
     * @param SagaDoneEvent $event
     */
    public function onSagaDone(SagaDoneEvent $event)
    {
        $this->endState = $event->getState();
    }

    public function getState() : State
    {
        return $this->endState;
    }
}