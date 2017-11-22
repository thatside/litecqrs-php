<?php

namespace LiteCQRS\Saga;

use LiteCQRS\Saga\State\State;

abstract class AbstractSaga implements SagaInterface
{
    /**
     * {@inheritDoc}
     */
    public function handle($event, State $state) : State
    {
        $method = $this->getHandleMethod($event);

        if (! method_exists($this, $method)) {
            throw new \BadMethodCallException(
                sprintf(
                    "No handle method '%s' for event '%s'.",
                    $method,
                    get_class($event)
                )
            );
        }

        return $this->$method($event, $state);
    }

    private function getHandleMethod($event) : string
    {
        $classParts = explode('\\', get_class($event));

        return 'on' . end($classParts);
    }
}