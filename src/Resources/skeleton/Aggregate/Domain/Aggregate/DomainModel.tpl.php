<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Shared\Domain\Aggregate\AggregateRoot;
use DateTimeImmutable;

<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isValueObject()): ?>
		use <?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst($attribute->getName()) ?>;
	<?php endif; ?>
<?php endforeach; ?>

/**
* Class <?= $entity_class_name ?>
* Aggregate Root of the <?= $entity_class_name ?> context.
*/
class <?= $entity_class_name ?> extends AggregateRoot
{
public function __construct(

<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isValueObject()): ?>
		public ? <?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst(
			$attribute->getName()
		) ?> $<?= $attribute->getName() ?>,
	<?php else: ?>
		public ? <?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
	<?php endif; ?>
<?php endforeach; ?>
) {}

public static function create(
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isValueObject()): ?>
		?<?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst(
			$attribute->getName()
		) ?> $<?= $attribute->getName() ?>,
	<?php else: ?>
		?<?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
	<?php endif; ?>
<?php endforeach; ?>
): self {
return new self(
<?php foreach ($attributes as $attribute): ?>
	$<?= $attribute->getName() ?>,
<?php endforeach; ?>
);
}

public function update(
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isValueObject()): ?>
		?<?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst(
			$attribute->getName()
		) ?> $<?= $attribute->getName() ?>,
	<?php else: ?>
		?<?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
	<?php endif; ?>
<?php endforeach; ?>
): self {
return new self(
<?php foreach ($attributes as $attribute): ?>
	$<?= $attribute->getName() ?>,
<?php endforeach; ?>
);
}
}
