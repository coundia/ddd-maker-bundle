<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* Class <?= $class_name ?>
* Query for fetching paginated <?= $entity_class_name ?> records with optional filters.
*/
class <?= $class_name ?>
{

public function __construct(
public int $page = 1,
public int $limit = 10,
public array $filters = []
) {

}
}
