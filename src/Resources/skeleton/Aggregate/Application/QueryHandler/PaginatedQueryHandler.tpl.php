<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Doctrine\ORM\EntityManagerInterface;
use <?= $entity_full_class ?>;
use <?= $query_full_class ?>;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;

#[AsMessageHandler]
class <?= $class_name ?>
{

public function __construct(
private EntityManagerInterface $entityManager,
private <?= $mapper_full_class ?> $mapper
)
{

}

public function __invoke(<?= $query_full_class ?> $query): array
{
$page = $query->page;
$limit = $query->limit;
$offset = ($page - 1) * $limit;

// Build the query to fetch paginated data with filters applied
$qb = $this->entityManager->createQueryBuilder();
$qb->select('e')
->from(<?= $entity_full_class ?>::class, 'e')
->setFirstResult($offset)
->setMaxResults($limit);

if (!empty($query->filters) && is_array($query->filters)) {
foreach ($query->filters as $field => $value) {
if ($value !== null && $value !== '') {
if (is_string($value) && strpos($value, '%') !== false) {
$qb->andWhere("e.$field LIKE :$field")
->setParameter($field, $value);
} else {
$qb->andWhere("e.$field = :$field")
->setParameter($field, $value);
}
}
}
}

$result = $qb->getQuery()->getResult();

// Build a count query with the same filters to determine total records
$countQb = $this->entityManager->createQueryBuilder();
$countQb->select('COUNT(e.id)')
->from(<?= $entity_full_class ?>::class, 'e');

if (!empty($query->filters) && is_array($query->filters)) {
foreach ($query->filters as $field => $value) {
if ($value !== null && $value !== '') {
if (is_string($value) && strpos($value, '%') !== false) {
$countQb->andWhere("e.$field LIKE :$field")
->setParameter($field, $value);
} else {
$countQb->andWhere("e.$field = :$field")
->setParameter($field, $value);
}
}
}
}
$total = (int) $countQb->getQuery()->getSingleScalarResult();

$data =  array_map(
fn($entity) => $this->mapper->toArray($this->mapper->fromEntity($entity)),
$result
);

return [
'data' => $data,
'total' => $total,
];
}
}
