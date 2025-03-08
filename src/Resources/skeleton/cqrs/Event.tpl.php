<?= "<?php\n" ?>
declare(strict_types=1);

/**
* Class <?= $class_name ?>
* Event class for <?= $entity_class_name ?>.
*/
namespace <?= $namespace ?>;

use Symfony\Contracts\EventDispatcher\Event;
use <?= $entity_full_class ?>;
use DateTimeImmutable;

class <?= $class_name ?> extends Event
{
public const NAME = '<?= $event_name ?>';

<?php foreach ($attributes as $attribute): ?>
    public ?<?= $attribute->getType() ?> $<?= $attribute->getName() ?>;
<?php endforeach; ?>

public function __construct(
<?php
$count = count($attributes);
$i = 0;
foreach ($attributes as $attribute):
	$i++;
	echo "        ?" . $attribute->getType() . " $" . $attribute->getName();
	echo $i < $count ? ",\n" : "\n";
endforeach; ?>
) {
<?php foreach ($attributes as $attribute): ?>
    $this-><?= $attribute->getName() ?> = $<?= $attribute->getName() ?>;
<?php endforeach; ?>
}
}
