<?= "<?php\n" ?>
declare(strict_types=1);

/**
* Class <?= $class_name ?>
* Listens for the <?= $event_constant ?> event and handles it.
*/
namespace <?= $namespace ?>;

use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use <?= $event_class ?>;

#[AsEventListener(event: <?= $event_class_short ?>::NAME)]
class <?= $class_name ?>
{
public function __invoke(<?= $event_class_short ?> $event): void
{
// Implement your event handling logic for the <?= $event_constant ?> event.
}
}
