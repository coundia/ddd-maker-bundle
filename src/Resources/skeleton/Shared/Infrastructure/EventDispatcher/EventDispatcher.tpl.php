<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Contracts\EventDispatcher\EventDispatcherInterface;

class <?= $class_name ?> implements <?= $interface ?>
{
public function __construct(
private EventDispatcherInterface $eventDispatcher
) {
}

#[\Override]
public function dispatch(array $events): void
{
foreach ($events as $event) {
$this->eventDispatcher->dispatch($event, $event::class);
}
}
}