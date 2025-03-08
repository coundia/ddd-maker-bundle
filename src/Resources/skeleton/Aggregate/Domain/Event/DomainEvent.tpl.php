<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use \App\Shared\Domain\Event\DomainEventInterface;

use DateTimeImmutable;

/** Event triggered when <?= $entity_class_name ?> is modified */
class <?= $class_name ?> implements DomainEventInterface
{
public function __construct(
public readonly <?= $model_object_value_id ?> $id,
public readonly DateTimeImmutable $occurredOn
) {}

public function occurredOn(): DateTimeImmutable
{
return $this->occurredOn;
}
}
