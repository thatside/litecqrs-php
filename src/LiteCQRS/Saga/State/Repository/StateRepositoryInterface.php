<?php


namespace LiteCQRS\Saga\State;


interface StateRepositoryInterface
{
    public function findOneBy(Criteria $criteria, string $sagaClass) : ?State;

    public function save(State $state, string $sagaClass) : void;
}