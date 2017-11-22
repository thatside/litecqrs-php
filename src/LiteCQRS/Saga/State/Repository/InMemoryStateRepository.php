<?php

namespace LiteCQRS\Saga\State\Repository;

use LiteCQRS\Saga\State\Criteria;
use LiteCQRS\Saga\State\State;
use LiteCQRS\Saga\State\StateRepositoryInterface;

class InMemoryStateRepository implements StateRepositoryInterface
{
    private $states;

    public function __construct()
    {
        $this->states = [];
    }

    public function findOneBy(Criteria $criteria, string $sagaClass) : ?State
    {
        if (! isset($this->states[$sagaClass])) {
            return null;
        }

        $states = $this->states[$sagaClass];

        foreach ($criteria->getComparisons() as $key => $value) {
            $states = array_filter($states, function (State $elem) use ($key, $value) {
                $stateValue = $elem->get($key);

                return is_array($stateValue) ? in_array($value, $stateValue) : $value === $stateValue;
            });
        }

        $amount = count($states);

        if (1 === $amount) {
            return current($states);
        }

        if ($amount > 1) {
            throw new \LogicException('Multiple saga state instances found.');
        }

        return null;
    }

    public function save(State $state, string $sagaClass) : void
    {
        $this->states[$sagaClass][$state->getId()] = $state;
    }
}