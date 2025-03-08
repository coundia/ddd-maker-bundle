<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* Interface <?= $class_name ?>
* Defines the contract for deleting a <?= $entity_class_name ?>.
*/
interface <?= $class_name ?>
{
public function delete(<?= $entity_full_class_id ?> $id): <?= $entity_full_class_id ?>;
}
