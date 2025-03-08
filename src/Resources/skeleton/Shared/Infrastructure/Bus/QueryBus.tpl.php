<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;


use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;

class <?= $class_name ?> implements <?= $interface ?>
{

public function __construct(private MessageBusInterface $queryBus)
{
}

public function dispatch(object $query,array $stamps = []): mixed
{
$envelope = $this->queryBus->dispatch($query);
$handledStamp = $envelope->last(HandledStamp::class);

if (!$handledStamp) {
throw new \RuntimeException(sprintf('No handler found for query of type "%s".', $query::class));
}

return $handledStamp->getResult();
}
}