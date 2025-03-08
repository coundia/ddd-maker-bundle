<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $repository_interface ?>;
use <?= $entity_full_class_id ?>;

/**
* Class <?= $class_name ?>DeleteService
* Deletes a <?= $entity_class_name ?> item.
*/
class <?= $class_name ?>DeleteService implements <?= $use_case ?>
{
public function __construct(private <?= $repository_interface ?> $repository)
{
}

/**
* Deletes a <?= $entity_class_name ?> by its ID.
*/
public function delete(
<?= $entity_full_class_id ?> $modelId
): <?= $entity_full_class_id ?> {

return  $this->repository->delete($modelId);
}
}
