<?php

namespace MyApp;

use LiteCQRS\Bus\DirectCommandBus;
use LiteCQRS\Bus\InMemoryEventMessageBus;
use LiteCQRS\DefaultDomainEvent;
use LiteCQRS\Saga\AbstractSaga;
use LiteCQRS\Saga\Metadata\StaticallyConfiguredSagaInterface;
use LiteCQRS\Saga\State\Criteria;
use LiteCQRS\Saga\State\State;
use Ramsey\Uuid\Uuid;

class ReservationSaga extends AbstractSaga implements StaticallyConfiguredSagaInterface
{
    private $commandBus;

    public function __construct(
        DirectCommandBus $commandBus
    ) {
        $this->commandBus = $commandBus;
    }

    public static function configuration()
    {
        return [
            'OrderPlaced' => function (OrderPlaced $event) {
                return null; // no criteria, start of a new saga
            },
            'ReservationAccepted' => function (ReservationAccepted $event) {
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId()
                ]);
            },
            'ReservationRejected' => function (ReservationRejected $event) {
                // return a Criteria object to fetch the State of this saga
                return new Criteria([
                    'reservationId' => $event->reservationId()
                ]);
            }
        ];
    }

    public function onOrderPlaced(OrderPlaced $event, State $state)
    {
        // keep the order id, for reference in `handleReservationAccepted()` and `handleReservationRejected()`
        $state->set('orderId', $event->orderId());

        // generate an id for the reservation
        $reservationId = Uuid::uuid4()->toString();
        $state->set('reservationId', $reservationId);

        // make the reservation
        $command = new MakeSeatReservation($reservationId, $event->numberOfSeats());
        $this->commandBus->dispatch($command);

        return $state;
    }

    public function onReservationAccepted(ReservationAccepted $event, State $state)
    {
        // the seat reservation for the given order is has been accepted, mark the order as booked
        $command = new MarkOrderAsBooked($state->get('orderId'));
        $this->commandBus->dispatch($command);

        // the saga ends here
        $state->setDone();

        return $state;
    }

    public function onReservationRejected(ReservationRejected $event, State $state)
    {
        // the seat reservation for the given order is has been rejected, reject the order as well
        $command = new RejectOrder($state->get('orderId'));
        $this->commandBus->dispatch($command);

        // the saga ends here
        $state->setDone();

        return $state;
    }
}

class ReservationCommandHandler
{
    private $eventBus;

    public function __construct(InMemoryEventMessageBus $bus)
    {
        $this->eventBus = $bus;
    }

    public function makeSeatReservation(MakeSeatReservation $command)
    {
        if(true) {
            echo "Reservation successful, accepting\n";
            $this->eventBus->publish(new ReservationAccepted(array('reservationId' => $command->reservationId())));
        } else {
            echo "Reservation unsuccessfull, rejecting\n";
            $this->eventBus->publish(new ReservationRejected(array('reservationId' => $command->reservationId())));
        }
    }


    public function markOrderAsBooked(MarkOrderAsBooked $command)
    {
        echo "Order booked\n";
    }

    public function rejectOrder(RejectOrder $order)
    {
        echo "Order rejected\n";
    }
}

/**
 * event
 */
class OrderPlaced extends DefaultDomainEvent
{
    protected $orderId;
    protected $numberOfSeats;

    public static function create($orderId, $numberOfSeats)
    {
        return new self(['orderId' => $orderId, 'numberOfSeats' => $numberOfSeats]);
    }

    public function orderId()
    {
        return $this->orderId;
    }

    public function numberOfSeats()
    {
        return $this->numberOfSeats;
    }
}

/**
 * command
 */
class MakeSeatReservation
{
    private $reservationId;
    private $numberOfSeats;

    public function __construct($reservationId, $numberOfSeats)
    {
        $this->reservationId = $reservationId;
        $this->numberOfSeats = $numberOfSeats;
    }

    public function reservationId()
    {
        return $this->reservationId;
    }

    public function numberOfSeats()
    {
        return $this->numberOfSeats;
    }
}

/**
 * event
 */
class ReservationAccepted extends DefaultDomainEvent
{
    protected $reservationId;

    public static function create($reservationId)
    {
        return new self(['reservationId' => $reservationId]);
    }

    public function reservationId()
    {
        return $this->reservationId;
    }
}

/**
 * event
 */
class ReservationRejected extends DefaultDomainEvent
{
    protected $reservationId;

    public static function create($reservationId)
    {
        return new self(['reservationId' => $reservationId]);
    }

    public function reservationId()
    {
        return $this->reservationId;
    }
}

/**
 * command
 */
class MarkOrderAsBooked
{
    private $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }
}

/**
 * command
 */
class RejectOrder
{
    private $orderId;

    public function __construct($orderId)
    {
        $this->orderId = $orderId;
    }
}