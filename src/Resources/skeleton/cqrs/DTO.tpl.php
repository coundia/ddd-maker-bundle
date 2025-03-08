<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $entity_full_class ?>;
use DateTimeImmutable;

/**
* Class <?= $class_name ?>
* Data Transfer Object for <?= $entity_class_name ?>.
*/
class <?= $class_name ?>
{
public function __construct(
<?php
$count = count($attributes);
$i = 0;
foreach ($attributes as $attribute) {
	$i++;
	echo "        public " . $attribute->getType() . " $" . $attribute->getName();
	echo $i < $count ? ",\n" : "\n";
}
?>
) {
}

public static function fromEntity(<?= $entity_class_name ?> $entity): self
{
return new self(
<?php
$i = 0;
foreach ($attributes as $attribute) {
	$i++;
	// Use direct property access since the entity properties are public.
	echo "            " . $attribute->getName() . ": \$entity->" . $attribute->getName();
	echo $i < $count ? ",\n" : "\n";
}
?>
);
}

public function toEntity(): <?= $entity_class_name ?>
{
return new <?= $entity_class_name ?>(<?php
$i = 0;
foreach ($attributes as $attribute) {
	$i++;
	echo "\n            " . $attribute->getName() . ": \$this->" . $attribute->getName();
	echo $i < $count ? "," : "";
}
echo "\n        ";
?>);
}
}
