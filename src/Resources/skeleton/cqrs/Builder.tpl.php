<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use <?= $command_full_class ?>;
use \DateTimeImmutable;
 

/**
* Class <?= $class_name ?>
* Builds a <?= $entity_class_name ?> entity from given data.
*/
class <?= $class_name ?>
{
<?php foreach ($attributes as $attribute): ?>
    private <?= $attribute->getType() ?> $<?= $attribute->getName() ?>;
<?php endforeach; ?>

public static function fromDTO(<?= $command_class_name ?> $dto): self
{
$builder = new self();
<?php foreach ($attributes as $attribute): ?>
    $builder-><?= $attribute->getName() ?> = $dto-><?= $attribute->getName() ?>;
<?php endforeach; ?>
return $builder;
}

<?php foreach ($attributes as $attribute): ?>

    public function set<?= ucfirst($attribute->getName()) ?>(<?= $attribute->getType() ?> $<?= $attribute->getName() ?>): self
    {
    $this-><?= $attribute->getName() ?> = $<?= $attribute->getName() ?>;
    return $this;
    }
<?php endforeach; ?>

public function build(): <?= $entity_class_name ?>
{
return new <?= $entity_class_name ?>(<?php
$args = [];
foreach ($attributes as $attribute) {
	$args[] = $attribute->getName() . ': $this->' . $attribute->getName();
}
echo implode(",\n            ", $args);
?>);
}
}
