<?php


namespace LiteCQRS\Saga\State\Manager;


use LiteCQRS\Saga\State\Criteria;
use LiteCQRS\Saga\State\State;

interface StateManagerInterface
{
    public function get(Criteria $criteria, string $sagaClass) : ?State;

    public function save(State $state, string $sagaClass) : void;
}