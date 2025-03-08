<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use <?= $entity_model_class ?>;

class <?= $class_name ?> implements <?= $interface_class ?>
{
public function __construct(
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isEntity()): ?>
		private \App\<?= $context ?>\Domain\UseCase\<?= ucfirst($attribute->getName()) ?>FindInterface $finder<?= ucfirst(
			$attribute->getName()) ?>,
		private \App\<?= $context ?>\Application\Mapper\<?= ucfirst($attribute->getName()) ?>\<?= ucfirst($attribute->getName()) ?>Mapper $mapper<?= ucfirst(
			$attribute->getName()) ?>,
	<?php endif; ?>
<?php endforeach; ?>
) {}

public function fromEntity(<?= $entity_class_name ?> $entity): <?= $entity_model_class ?>
{
return new <?= $entity_model_class ?>(
<?php foreach ($attributes as $attribute): ?>
	<?php $getter = $attribute->getGetterMethod($attribute->getName());$caster = '' ?><?php if (!$getter) continue; ?>
	<?php if ($attribute->isTypeUuid($attribute->getType())): ?><?php $caster = '';$getter .= "->toString()"; ?><?php endif; ?>
	<?php if ($attribute->isEntity()): ?><?php $caster = '(string)';$getter .= "->getId()"; ?><?php endif; ?>
	<?php if ($attribute->isValueObject()): ?><?= $attribute->getName() ?>: <?= $attribute->getObjectValue($attribute->getName()) ?>::create(<?= $caster ?>$entity-><?= $getter ?>),
		<?php continue; endif; ?> <?= $attribute->getName() ?>: $entity-><?= $getter ?>,
<?php endforeach; ?>);
}
public function toEntity(<?= $entity_model_class ?> $model): <?= $entity_class_name ?>
{
return new <?= $entity_class_name ?>(
<?php foreach ($attributes as $attribute): ?>
	<?php $value ='value()'; if (!$attribute->isOnConstrutor) continue; ?>
	<?php if ($attribute->isTypeDate()){ $value = 'dateValue()'; }?>
	<?php if ($attribute->isEntity()): ?><?php $caster = '(string)';$getter .= $attribute->getName()."->value" ;  ?>
		<?= $attribute->getName() ?>: $this->mapper<?= ucfirst(
			$attribute->getName()
		) ?>->toEntity($this->finder<?= ucfirst(
			$attribute->getName()
		) ?>->find(\App\<?= $context ?>\Domain\ValueObject\<?= ucfirst($attribute->getName()) ?>Id::create(<?= $caster ?> $model-><?= lcfirst($attribute->getName()) ?>-><?= $value ?>))),
		<?php continue;  endif; ?>
	<?php if ($attribute->isValueObject()): ?>
		<?= $attribute->getName() ?>: $model-><?= $attribute->getName() ?>?-><?= $value ?>,
		<?php continue; endif; ?>
	<?= $attribute->getName() ?>: $model-><?= $attribute->getName() ?>,
<?php endforeach; ?>
);
}

public function fromArray(array $data): <?= $entity_model_class ?>
{
return new <?= $entity_model_class ?>(
<?php foreach ($attributes as $attribute): ?>
	<?php $getter = $attribute->getGetterMethod($attribute->getName());
	$caster = '' ?>
	<?php if ($attribute->isValueObject()): ?>
		<?= $attribute->getName() ?>: <?= $attribute->getObjectValue(
			$attribute->getName()
		) ?>::create(<?= $caster ?>$data['<?= $attribute->getName() ?>'] ?? null),
		<?php continue; endif; ?>
	<?= $attribute->getName() ?>: <?php
	if ($attribute->isTypeDate()){
		?>isset($data['<?= $attribute->getName() ?>']) ? new
		\DateTimeImmutable($data['<?= $attribute->getName() ?>'] ?? null) : null<?php
	} else{
		?>$data['<?= $attribute->getName() ?>'] ?? null<?php
	}
	?>,
<?php endforeach; ?>
);
}

public function toArray(<?= $entity_model_class ?> $model): array
{
return [
<?php foreach ($attributes as $attribute): ?>
	<?php $value ='valueView()'; ?>
	'<?= $attribute->getName() ?>' => <?php
	if ($attribute->isValueObject()){
		?>$model-><?= $attribute->getName() ?>?-><?= $value ?>,
		<?php continue;
	}
	if ($attribute->isTypeDate()){
		?>$model-><?= $attribute->getName() ?>?->format('Y-m-d H:i:s') <?php
	} else{
		?>$model-><?= $attribute->getName() ;} ?>
	,
<?php endforeach; ?>
]  ;
}

}
