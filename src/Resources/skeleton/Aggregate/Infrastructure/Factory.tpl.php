<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use DateTimeImmutable;

/**
* Class <?= $class_name ?>.
* Creates <?= $entity_class_name ?> entities for testing purposes.
* Uses Zenstruck Foundry to generate persistent test data.
*/
final class <?= $class_name ?> extends PersistentProxyObjectFactory
{
public function __construct()
{
}

public static function class(): string
{
return <?= $entity_class_name ?>::class;
}

protected function defaults(): array|callable
{
return [
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType(),'self::','',true) ?>,
<?php endforeach; ?>
];
}

protected function initialize(): static
{
return $this;
}
}
