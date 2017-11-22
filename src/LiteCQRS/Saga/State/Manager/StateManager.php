<?php

namespace LiteCQRS\Saga\State\Manager;

use LiteCQRS\Saga\State\Criteria;
use LiteCQRS\Saga\State\State;
use LiteCQRS\Saga\State\StateRepositoryInterface;
use Ramsey\Uuid\Uuid;

class StateManager implements StateManagerInterface
{
    private $repository;

    public function __construct(StateRepositoryInterface $repository)
    {
        $this->repository = $repository;
    }

    public function get(?Criteria $criteria, string $sagaClass) : ?State
    {
        if ($criteria instanceof Criteria) {
            return $this->repository->findOneBy($criteria, $sagaClass);
        }

        return new State(Uuid::uuid4()->toString());
    }

    public function save(State $state, string $sagaClass) : void
    {
        $this->repository->save($state, $sagaClass);
    }
}