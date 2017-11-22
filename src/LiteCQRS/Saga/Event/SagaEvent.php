<?php

namespace LiteCQRS\Saga\Event;

use LiteCQRS\DefaultDomainEvent;

class SagaEvent extends DefaultDomainEvent
{
    protected $sagaClass;
    protected $sagaState;

    public static function create($sagaClass, $sagaState)
    {
        return new self(['sagaClass' => $sagaClass, 'sagaState' => $sagaState]);
    }
}