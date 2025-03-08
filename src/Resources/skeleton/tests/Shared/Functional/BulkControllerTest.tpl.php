<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $base_test_case ?>;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\Response;
use function Zenstruck\Foundry\faker;

class <?= $class_name ?> extends <?= $base_test_case ?>
{
use Factories;

#[\PHPUnit\Framework\Attributes\Test]
public function testCreateEntity(): void
{
$bulk = array_map(fn() => [
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType()) ?>,
<?php endforeach; ?>
], range(1, 3));

$response = $this->post('/api/<?= $api_version ?>/<?= $entity_name_plural ?>/bulk', $bulk);

$response->assertStatusCode(Response::HTTP_CREATED);

$content = $response->getData();

$this->assertIsArray($content);
$this->assertCount(3, $content);

foreach ($content as $entity) {
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>

	$this->assertArrayHasKey('<?= $attribute->getName() ?>', $entity);


<?php endforeach; ?>
}
}
}
