<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* Interface <?= $class_name ?>
* Defines the contract for querying <?= $entity_class_name ?> entities.
*/
interface <?= $class_name ?>
{
public function find(<?= $entity_full_class_id ?> $id): ?<?= $entity_full_class ?>;

public function findAll(): array;

public function findPaginated(int $page, int $limit, array $criteria = []): array;
}
