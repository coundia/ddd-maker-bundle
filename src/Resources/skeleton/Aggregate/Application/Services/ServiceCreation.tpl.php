<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use <?= $model ?>;
use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;
use <?= $repository_interface ?>;
use Symfony\Component\Security\Core\User\UserInterface;
use DateTimeImmutable;

/**
* Class <?= $class_name ?>
* Creates a new <?= $entity_class_name ?> item.
*/
class <?= $class_name ?> implements <?= $use_case ?>
{
public function __construct(private <?= $repository_interface ?> $repository)
{
}

/**
* Creates a new <?= $entity_class_name ?> using a DTO.
*/
public function create (
<?= $model ?> $model
): <?= $model ?> {

return  $this->repository->save($model);
}
}
