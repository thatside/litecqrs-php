<?php


namespace LiteCQRS\Saga\State\Repository;


use LiteCQRS\Saga\State\Criteria;
use LiteCQRS\Saga\State\State;

interface StateRepositoryInterface
{
    public function findOneBy(Criteria $criteria, string $sagaClass) : ?State;

    public function save(State $state, string $sagaClass) : void;
}