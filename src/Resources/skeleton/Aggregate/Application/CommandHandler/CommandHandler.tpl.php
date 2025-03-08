<?= "<?php\n" ?>
/**
*
* Class <?= $class_name ?>
*
*
* @see <?= $command_full_class ?>
*
*/
declare(strict_types=1);

namespace <?= $namespace ?>;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Messenger\Attribute\AsMessageHandler;
use <?= $entity_full_class ?>;

#[AsMessageHandler]
class <?= $class_name ?>Handler
{

public function __construct(
private EntityManagerInterface $entityManager,
private <?= $mapper_full_class ?> $mapper
)
{
}

public function __invoke(<?= $command_full_class ?> $command): <?= $model ?>
{

$entity = new <?= $entity_class_name ?>(
<?php foreach ($attributes as $attribute): ?>
	<?php $value ='value()'; if (!$attribute->isOnConstrutor) continue; ?>
	<?php if ($attribute->isTypeDate()){ $value = 'dateValue()'; }?>
	<?php if ($attribute->isEntity()): ?><?php $caster = '(string)';$getter .= $attribute->getName()."->value" ;  ?>
		<?= $attribute->getName() ?>: $this->mapper<?= ucfirst(
			$attribute->getName()
		) ?>->toEntity($this->finder<?= ucfirst(
			$attribute->getName()
		) ?>->find(\App\<?= $context ?>\Domain\ValueObject\<?= ucfirst($attribute->getName()) ?>Id::create(<?= $caster ?> $command-><?= lcfirst($attribute->getName()) ?>-><?= $value ?>))),
		<?php continue;  endif; ?>
	<?php if ($attribute->isValueObject()): ?>
		<?= $attribute->getName() ?>: $command-><?= $attribute->getName() ?>?-><?= $value ?>,
		<?php continue; endif; ?>
	<?= $attribute->getName() ?>: $command-><?= $attribute->getName() ?>,
<?php endforeach; ?>
);

$this->entityManager->persist($entity);
$this->entityManager->flush();

return $this->mapper->fromEntity($entity);
}
}
