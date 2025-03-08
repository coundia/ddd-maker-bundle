<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use <?= $entity_model_class ?>;

interface <?= $interface_class ?>
{
public function fromEntity(<?= $entity_class_name ?> $entity): <?= $entity_model_class ?>;
public function toEntity(<?= $entity_model_class ?> $model): <?= $entity_class_name ?>;
public function fromArray(array $data): <?= $entity_model_class ?>;
public function toArray(<?= $entity_model_class ?> $model): array;
}
