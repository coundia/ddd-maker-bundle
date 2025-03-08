<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use DateTimeImmutable;

/** Base interface for all domain events */
interface DomainEventInterface
{
public function occurredOn(): DateTimeImmutable;
}
