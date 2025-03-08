<?= "<?php\n" ?>
declare(strict_types=1);

namespace App\Query;

use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\HandledStamp;
use RuntimeException;

final class BaseQueryBus
{
private MessageBusInterface $queryBus;

public function __construct(MessageBusInterface $queryBus)
{
$this->queryBus = $queryBus;
}

public function dispatch(object $query): mixed
{
$envelope = $this->queryBus->dispatch($query);
$handledStamp = $envelope->last(HandledStamp::class);
if (!$handledStamp) {
throw new RuntimeException(sprintf(
'No handler found for query of type "%s".',
get_class($query)
));
}
return $handledStamp->getResult();
}
}
