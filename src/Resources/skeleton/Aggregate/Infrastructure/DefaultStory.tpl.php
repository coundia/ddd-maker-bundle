<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Zenstruck\Foundry\Story;

/**
* Class <?= $class_name ?>.
* Story to create <?= $count ?> instances of <?= $entity_class_name ?> using the factory.
*/
final class <?= $class_name ?> extends Story
{
public function build(): void
{
\<?= $factory_full_class ?>::createMany(<?= $count ?>);
}
}
