<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use <?= $model ?>;
use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;
use <?= $repository_interface ?>;
use <?= $entity_full_class_id ?>;

use Symfony\Component\Security\Core\User\UserInterface;

/**
* Class <?= $class_name ?>UpdateService
* Updates an existing <?= $entity_class_name ?> item.
*/
class <?= $class_name ?>UpdateService implements <?= $use_case ?>
{
public function __construct(private <?= $repository_interface ?> $repository)
{
}

/**
* Updates an existing <?= $entity_class_name ?> using a DTO.
*/
public function update (
<?= $model ?> $model,
<?= $entity_full_class_id ?> $id
): <?= $model ?> {

return  $this->repository->update($model,$id);
}
}
