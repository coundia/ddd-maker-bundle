<?= "<?php\n" ?>

declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $model ?>;
use <?= $entity_full_class ?>;
use <?= $entity_full_class_id ?>;
use <?= $repository_interface ?>;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NoResultException;


class <?= $entity_class_name ?>Repository implements <?= $repository_interface ?>
{
public function __construct(
private EntityManagerInterface $em,
private \App\<?= $context ?>\Application\Mapper\<?= $entity_class_name ?>\<?= $entity_class_name ?>MapperInterface $mapper
)
{
}

public function save(<?= $model ?> $<?= strtolower($entity_class_name) ?>): <?= $model ?>
{
$entity = new <?= $entity_class_name ?>(
<?php foreach ($attributes as $attribute): ?>
	<?php if (!$attribute->isOnConstrutor) continue; ?>
	<?php $setter = $attribute->getSetterMethod($attribute->getName()); $value = "value()"; ?>
	<?php if ($attribute->isTypeDate()){ $value = 'dateValue()'; }?>
	<?php if ($attribute->isEntity()){ ?>
		<?= $attribute->getName() ?>: $this->em->find(\<?= $attribute->getType() ?>::class, $<?= strtolower(
			$entity_class_name
		) ?>-><?= $attribute->getName() ?>?-><?= $value ?>),
		<?php continue; ?>
	<?php } ?>
	<?= $attribute->getName() ?>: <?php
	if ($attribute->isValueObject()){
		?>$<?= strtolower($entity_class_name) ?>-><?= $attribute->getName() ?>?-><?= $value ?><?php
	} else{
		?>$<?= strtolower($entity_class_name) ?>-><?= $attribute->getName() ?><?php
	}
	?>,
<?php endforeach; ?>
);

if (!$this->em->contains($entity)) {
$this->em->persist($entity);
}

$this->em->flush();

return $this->mapper->fromEntity($entity);
}

public function update(<?= $model ?> $<?= strtolower(
	$entity_class_name
) ?>, <?= $entity_class_name ?>Id $id): <?= $model ?>
{
$entity = $this->em->find(<?= $entity_class_name ?>::class, $id?->value());

if ($entity) {
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	<?php $setter = $attribute->getSetterMethod($attribute->getName()); $value = "value()"; ?>
	<?php if ($attribute->isTypeDate()){ $value = 'dateValue()'; }?>
	<?php if (!$setter) continue; ?>
	<?php if ($attribute->isEntity()){ ?>
		$entity-><?= $setter ?>($this->em->find(\<?= $attribute->getType() ?>::class, $<?= strtolower(
			$entity_class_name
		) ?>-><?= $attribute->getName() ?>?-><?= $value ?>));
		<?php continue; ?>
	<?php } ?>
	<?php if ($attribute->isValueObject()): ?>
		$entity-><?= $setter ?>($<?= strtolower($entity_class_name) ?>-><?= $attribute->getName() ?>?-><?= $value ?>);
	<?php else: ?>
		$entity-><?= $setter ?>($<?= strtolower($entity_class_name) ?>-><?= $attribute->getName() ?>);
	<?php endif; ?>
<?php endforeach; ?>
$this->em->flush();
}

return $this->mapper->fromEntity($entity);
}

public function find(<?= $entity_class_name ?>Id $id): ?<?= $model ?>
{
$entity = $this->em->find(<?= $entity_class_name ?>::class, $id?->value());


return $entity ? $this->mapper->fromEntity($entity) : null;
}

public function delete(<?= $entity_class_name ?>Id $id): <?= $entity_class_name ?>Id
{
$entity = $this->em->find(<?= $entity_class_name ?>::class, $id?->value());

if ($entity) {
$this->em->remove($entity);
$this->em->flush();
}
return $id;
}

public function findById(<?= $entity_class_name ?>Id $id): ?<?= $model ?>
{
$qb = $this->em->createQueryBuilder();
$qb->select('e')
->from(<?= $entity_class_name ?>::class, 'e')
->where('e.id = :id')
->setParameter('id', $id?->value());

try {
$entity = $qb->getQuery()->getSingleResult();
return $this->mapper->fromEntity($entity);
} catch (NoResultException) {
return null;
}
}

/**
* @return null|array<<?= $model ?>>
*/
public function findAll(): array
{
return array_map(
fn($entity) => $this->mapper->fromEntity($entity),
$this->em->createQueryBuilder()
->select('e')
->from(<?= $entity_class_name ?>::class, 'e')
->getQuery()
->getResult()
);
}
/**
* @return null|array<<?= $model ?>>
*/
public function findByCriteria(array $criteria): array
{
$qb = $this->em->createQueryBuilder();
$qb->select('e')
->from(<?= $entity_class_name ?>::class, 'e');

foreach ($criteria as $field => $value) {
$qb->andWhere("e.$field = :$field")
->setParameter($field, $value);
}

return array_map(
fn($entity) => $this->mapper->fromEntity($entity),
$qb->getQuery()->getResult()
);
}

/**
* @return array{
*     items: array<<?= $model ?>>,
*     total: int,
*     page: int,
*     limit: int
* }
*/
public function findPaginated(int $page, int $limit, array $criteria = []): array
{
$qb = $this->em->createQueryBuilder()
->select('e')
->from(<?= $entity_class_name ?>::class, 'e');

foreach ($criteria as $field => $value) {
$qb->andWhere("e.$field = :$field")
->setParameter($field, $value);
}

$total = (clone $qb)
->select('COUNT(e.id)')
->getQuery()
->getSingleScalarResult();

$items = $qb->setFirstResult(($page - 1) * $limit)
->setMaxResults($limit)
->getQuery()
->getResult();

return [
'items' => array_map(
fn($entity) => $this->mapper->toArray($this->mapper->fromEntity($entity)),
$items
),
'total' => (int) $total,
'page' => $page,
'limit' => $limit,
];
}

}
