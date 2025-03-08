<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use DateTimeImmutable;

/**
* Class <?= $class_name ?> Represents a command for creating a <?= $entity_class_name ?>.
*/
class <?= $class_name ?>
{
public function __construct(

<?php foreach ($attributes as $attribute): ?>
	<?php if (!$attribute->isOnConstrutor) continue; ?>
	<?php if ($attribute->isValueObject()): ?>
		public ? \App\<?= $context ?>\Domain\ValueObject\<?= $entity_class_name ?><?= ucfirst(
			$attribute->getName()
		) ?> $<?= $attribute->getName() ?>,
	<?php else: ?>
		public ? <?= $attribute->getType() ?> $<?= $attribute->getName() ?>,
	<?php endif; ?>
<?php endforeach; ?>
) {}

}
