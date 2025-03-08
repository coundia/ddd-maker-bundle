<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $base_test_case ?>;
use <?= $entity_factory ?>;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\Response;
use function Zenstruck\Foundry\faker;

class <?= $class_name ?> extends <?= $base_test_case ?>
{
use Factories;

public function testListEntitiesWithPagination(): void
{
$entity = <?= $entity_factory ?>::createOne([
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType(), '', '', true) ?>,
<?php endforeach; ?>
])->_disableAutoRefresh();

<?= $entity_factory ?>::createMany(9);

$response = $this->get('/api/<?= $api_version ?>/<?= $entity_name_plural ?>?page=1&limit=5');

$response->assertStatusCode(Response::HTTP_OK);

$content = $response->getData();

$this->assertIsArray($content);
$this->assertArrayHasKey('items', $content);
$this->assertArrayHasKey('total', $content);
$this->assertArrayHasKey('page', $content);
$this->assertArrayHasKey('limit', $content);

$this->assertEquals(1, $content['page']);
$this->assertEquals(5, $content['limit']);
$this->assertEquals(10, $content['total']);
$this->assertCount(5, $content['items']);

$firstItem = $content['items'][0] ?? null;
$this->assertNotNull($firstItem);

<?php foreach ($attributes as $attribute): ?>

	<?php if ($attribute->isEntity()):?>
		$this->assertEquals($firstItem['<?= $attribute->getName() ?>'], $entity-><?= $attribute->getGetterMethod($attribute->getName())?>->getId());
		<?php continue; endif; ?>
	<?php if ($attribute->isTypeDate()):?>
		//$this->assertEquals($firstItem['<?= $attribute->getName() ?>'], $entity-><?= $attribute->getGetterMethod($attribute->getName())?>->format('Y-m-d H:i:s');
		<?php continue; endif; ?>

	$this->assertArrayHasKey('<?= $attribute->getName() ?>', $firstItem);
	$this->assertEquals($entity-><?= $attribute->getGetterMethod($attribute->getName()) ?>, $firstItem['<?= $attribute->getName() ?>']);

<?php endforeach; ?>
}
}
