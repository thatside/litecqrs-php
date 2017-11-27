<?php

namespace LiteCQRS\Saga\Event;

use LiteCQRS\DefaultDomainEvent;
use LiteCQRS\Saga\State\State;

class SagaEvent extends DefaultDomainEvent
{
    protected $sagaClass;
    protected $sagaState;

    public static function create(string $sagaClass, State $sagaState)
    {
        return new static(['sagaClass' => $sagaClass, 'sagaState' => $sagaState]);
    }

    public function getSagaClass() : string
    {
        return $this->sagaClass;
    }

    public function getState() : State
    {
        return $this->sagaState;
    }
}