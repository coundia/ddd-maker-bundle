<?= "<?php\n" ?>

namespace <?= $namespace ?>;

class <?= $class_name ?>
{

public function __construct(
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->getName() == lcfirst($parameter)): ?>
	<?php if ($attribute->isValueObject()): ?>
		public ? \App\<?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst(
			$attribute->getName()
		) ?> $<?= $attribute->getName() ?>,
	<?php else: ?>
		public ? <?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
	<?php endif; ?>
	<?php endif; ?>
<?php endforeach; ?>
)
{

}
}
