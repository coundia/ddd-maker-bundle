<?= "<?php\n" ?>

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

$parameter = $query-><?= $parameter ?>?->value();
$qb = $this->entityManager->createQueryBuilder();
$qb->select('e')
->from(<?= $entity_full_class ?>::class, 'e')
->where('e.<?= $parameter ?> = :parameter')
->setParameter('parameter', $parameter);

$result = $qb->getQuery()->getResult();

if(!$result) {
throw new \Exception('Not found');
}

$data =  array_map(
fn($entity) => $this->mapper->toArray($this->mapper->fromEntity($entity)),
$result
);

return $data;
}
}
