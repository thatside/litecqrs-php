<?php

namespace LiteCQRS\Bus;

use LiteCQRS\Saga\AbstractSaga;

class EventInvocationHandler implements MessageHandlerInterface
{
    private $service;

    public function __construct($service)
    {
        $this->service = $service;
    }

    public function handle($event)
    {
        $eventName = new EventName($event);
        $methodName = "on" . $eventName;

        $this->service->$methodName($event);
    }

    public function isSaga()
    {
        return is_subclass_of($this->service, AbstractSaga::class);
    }
}

