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
	<?php if ($attribute->isId()) continue; $generatedValue = $attribute->generateFakerValue($attribute->getType(), '', '', true); ?>
	'<?= $attribute->getName() ?>' => $value<?= ucfirst($attribute->getName()) ?> = <?= $generatedValue ?>,
<?php endforeach; ?>
])->_disableAutoRefresh();

<?= $entity_factory ?>::createMany(9);

$response = $this->get('/api/<?= $api_version ?>/<?= $entity_name_plural ?>');
$response->assertStatusCode(Response::HTTP_OK);
$content = $response->getData();
$this->assertIsArray($content);
$this->assertArrayHasKey('items', $content);
$this->assertArrayHasKey('total', $content);

$this->assertCount(10, $content['items']);
$this->assertEquals(10, $content['total']);

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
