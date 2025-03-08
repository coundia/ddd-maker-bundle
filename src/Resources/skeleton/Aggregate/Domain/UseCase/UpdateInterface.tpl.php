<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* Interface <?= $class_name ?>
* Defines the contract for updating <?= $entity_class_name ?> entities.
*/
interface <?= $class_name ?>
{
public function update(<?= $entity_full_class ?> $entity,<?= $entity_full_class_id ?> $entityId ): <?= $entity_full_class ?>;
}
