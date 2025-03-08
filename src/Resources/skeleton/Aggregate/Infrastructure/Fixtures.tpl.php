<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use <?= $story_full_class ?>;

/**
* Class <?= $class_name ?>.
* Seeds the database with initial data using the story.
*/
class <?= $class_name ?> extends Fixture
{
public function load(ObjectManager $manager): void
{
<?= $story_class ?>::load();
}
}
