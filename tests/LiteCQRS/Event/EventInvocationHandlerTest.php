<?php
/**
 * Created by PhpStorm.
 * User: thatside
 * Date: 20.11.17
 * Time: 11:58
 */

use LiteCQRS\Bus\EventInvocationHandler;
use PHPUnit\Framework\TestCase;

$__storage = ['sagaTested' => null, 'eventTested' => null];

class EventInvocationHandlerTest extends TestCase
{
    private $sagaInvocationHandler;
    private $eventInvocationHandler;

    public function setUp()
    {
        $this->sagaInvocationHandler = new EventInvocationHandler(new TestSaga());
        $this->eventInvocationHandler = new EventInvocationHandler(new TestHandler());
    }

    public function testSagaInvocationHandler()
    {
        $event = new TestEvent();
        $event->data = 'test data';

        $this->sagaInvocationHandler->handle($event);

        global $__storage;

        $this->assertEquals($event->data, $__storage['sagaTested']);
        $this->assertEquals(true, $this->sagaInvocationHandler->isSaga());
    }

    public function testEventInvocationHandler()
    {
        $event = new TestEvent();
        $event->data = 'test data';

        $this->eventInvocationHandler->handle($event);

        global $__storage;

        $this->assertEquals($event->data, $__storage['eventTested']);
        $this->assertEquals(false, $this->eventInvocationHandler->isSaga());
    }
}

class TestSaga extends \LiteCQRS\Saga\AbstractSaga
{
    public function onTest(TestEvent $event)
    {
        global $__storage;
        $__storage['sagaTested'] = $event->data;
    }
}

class TestHandler
{
    public function onTest(TestEvent $event)
    {
        global $__storage;
        $__storage['eventTested'] = $event->data;
    }
}

class TestEvent extends \LiteCQRS\DefaultDomainEvent
{
    public $data;
}