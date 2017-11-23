<?php

namespace LiteCQRS;

use LiteCQRS\Saga\AbstractSaga;
use LiteCQRS\Saga\State\State;
use PHPUnit\Framework\TestCase;
use Ramsey\Uuid\Uuid;

class SagaTest extends TestCase
{
    public function testSimpleSagaFlow()
    {
        $testSaga = new TestSaga();
        $testEvent = new MyEvent();
        $testState = new State(Uuid::uuid4()->toString());

        $this->assertEquals($testEvent->payload, $testSaga->handle($testEvent, $testState)->get('data'));
    }
}

class TestSaga extends AbstractSaga
{
    protected function onMyEvent(MyEvent $event, State $state)
    {
        $state->set('data', $event->payload);
        return $state;
    }
}

class MyEvent extends DefaultDomainEvent
{
    public $payload = 'done';
}