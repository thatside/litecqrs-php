<?php
namespace MyApp;

use LiteCQRS\Bus\DirectCommandBus;
use LiteCQRS\Bus\EventMessageHandlerFactory;
use LiteCQRS\Bus\IdentityMap\EventProviderQueue;
use LiteCQRS\Bus\IdentityMap\SimpleIdentityMap;
use LiteCQRS\Bus\InMemoryEventMessageBus;
use LiteCQRS\Bus\SagaMessageHandlerFactory;
use LiteCQRS\Saga\Metadata\StaticallyConfiguredSagaMetadataFactory;
use LiteCQRS\Saga\State\Manager\StateManager;
use LiteCQRS\Saga\State\Repository\InMemoryStateRepository;
use Ramsey\Uuid\Uuid;

require_once __DIR__ . "/../vendor/autoload.php";


$messageBus  = new InMemoryEventMessageBus();

$identityMap = new SimpleIdentityMap();
$queue = new EventProviderQueue($identityMap);
$commandBus  = new DirectCommandBus(array(
    new EventMessageHandlerFactory($messageBus, $queue)
));

$saga = new ReservationSaga($commandBus);
$commandHandler = new ReservationCommandHandler($messageBus);

$sagaMessageHandlerFactory = new SagaMessageHandlerFactory(
    new StateManager(new InMemoryStateRepository()),
    new StaticallyConfiguredSagaMetadataFactory(),
    $messageBus,
    $commandBus
);

$sagaMessageHandlerFactory->registerSagas(array(get_class($saga) => $saga));

$messageBus->registerProxyFactory($sagaMessageHandlerFactory);

$commandBus->register('MyApp\MakeSeatReservation', $commandHandler);
$commandBus->register('MyApp\MarkOrderAsBooked', $commandHandler);
$commandBus->register('MyApp\RejectOrder', $commandHandler);

$messageBus->register($saga);

$event = OrderPlaced::create(Uuid::uuid4()->toString(), 4);

$messageBus->dispatch($event);