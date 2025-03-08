<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* Interface <?= $class_name ?>
* Defines the contract for creating a <?= $entity_class_name ?>.
*/
interface <?= $class_name ?>
{
public function create(<?= $entity_full_class ?> $<?= lcfirst($entity_class_name) ?>): <?= $entity_full_class ?>;
}
