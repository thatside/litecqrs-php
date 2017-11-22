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
        $testEvent = new ExampleEvent();

        $this->assertEquals($testEvent->payload, $testSaga->handle($testEvent, new State(Uuid::uuid4())));
    }
}

class TestSaga extends AbstractSaga
{
    protected function handleExampleEvent(ExampleEvent $event, State $state)
    {
        return $event->payload;
    }
}

class ExampleEvent extends DefaultDomainEvent
{
    public $payload = 'done';
}