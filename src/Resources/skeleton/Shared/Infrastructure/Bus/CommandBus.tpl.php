<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;


use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class <?= $class_name ?> implements <?= $interface ?>
{


public function __construct(private MessageBusInterface $commandBus)
{
}

public function dispatch(object $command,array $stamps = []): mixed
{
$envelope = $this->commandBus->dispatch($command);
$handledStamp = $envelope->last(HandledStamp::class);

if (!$handledStamp) {
throw new \RuntimeException(sprintf('No handler found for command of type "%s".', $command::class));
}

return $handledStamp->getResult();
}
}