<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class_id ?>;
use <?= $model ?>;

interface <?= $entity_class_name ?>RepositoryInterface
{
public function save(<?= $model ?> $<?= strtolower($entity_class_name) ?>): <?= $model ?>;
public function update(<?= $model ?> $<?= strtolower(
	$entity_class_name
) ?>, <?= $entity_class_name ?>Id $id): <?= $model ?>;
public function delete(<?= $entity_class_name ?>Id $<?= strtolower($entity_class_name) ?>): <?= $entity_class_name ?>Id;
public function findById(<?= $entity_class_name ?>Id $id): ?<?= $model ?>;

/**
* @return array<<?= $model ?>>
*/
public function findAll(): array;

/**
* @return array<<?= $model ?>>
*/
public function findByCriteria(array $criteria): array;

/**
* @return array{
*     items: array<<?= $model ?>>,
*     total: int,
*     page: int,
*     limit: int
* }
*/
public function findPaginated(int $page, int $limit, array $criteria = []): array;
}
