<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $repository_interface ?>;
use <?= $entity_full_class_id ?>;
use <?= $DTONamespace ?>ResponseDTO;

<?php $dto = $DTONamespace . 'ResponseDTO'; ?>

/**
* Class <?= $class_name ?>FindService
* Fetches <?= $entity_class_name ?> items.
*/
class <?= $class_name ?>FindService implements <?= $use_case ?>
{
public function __construct(private <?= $repository_interface ?> $repository)
{
}

/**
* Finds a <?= $entity_class_name ?> by its ID.
*/
public function find(<?= $entity_class_name ?>Id $id): ?<?= $model ?>
{
return $this->repository->findById($id);
}

/**
* Finds all <?= $entity_class_name ?> items.
*/
public function findAll(): array
{
return  $this->repository->findAll();
}

/**
* Finds paginated <?= $entity_class_name ?> items.
*
*/
public function findPaginated(int $page, int $limit, array $criteria = []): array
{

return $this->repository->findPaginated($page, $limit, $criteria);
}
}
