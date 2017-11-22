<?php

namespace LiteCQRS\Saga;

use LiteCQRS\Saga\State\State;

interface SagaInterface
{
    public function handle($event, State $state) : State;
}