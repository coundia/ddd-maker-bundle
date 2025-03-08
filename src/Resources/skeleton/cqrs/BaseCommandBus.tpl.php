<?= "<?php\n" ?>
/**
* Class BaseCommandBus
* Dispatches commands using the message bus.
*/
declare(strict_types=1);

namespace App\Command;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use RuntimeException;

final class BaseCommandBus
{
private MessageBusInterface $commandBus;

public function __construct(MessageBusInterface $commandBus)
{
$this->commandBus = $commandBus;
}

public function dispatch(object $command): mixed
{
$envelope = $this->commandBus->dispatch($command);
$handledStamp = $envelope->last(HandledStamp::class);
if (!$handledStamp) {
throw new RuntimeException(sprintf(
'No handler found for command of type "%s".',
get_class($command)
));
}
return $handledStamp->getResult();
}
}
