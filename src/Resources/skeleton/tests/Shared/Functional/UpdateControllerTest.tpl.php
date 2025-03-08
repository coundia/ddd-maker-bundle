<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $base_test_case ?>;
use <?= $entity_factory ?>;
use Symfony\Component\HttpFoundation\Response;
use function Zenstruck\Foundry\faker;

class <?= $class_name ?> extends <?= $base_test_case ?>
{
public function testUpdateEntity(): void
{
self::markTestSkipped("Fix assertions and enable the test");
//$user = $this->user();

$entity = <?= $entity_factory ?>::createOne([
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType(), '', '', true) ?>,
<?php endforeach; ?>
])->_disableAutoRefresh();

$id = (string) $entity->getId();

$updatePayload = [
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType(), '', '->getId()', false) ?>,
<?php endforeach; ?>
];

$response = $this->put("/api/<?= $api_version ?>/<?= $entity_name_plural ?>/$id", $updatePayload);

$response->assertStatusCode(Response::HTTP_OK);

$updatedEntity = <?= $entity_factory ?>::find($id)->_disableAutoRefresh();
$this->assertNotNull($updatedEntity);

<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	<?php if ($attribute->isEntity()):?>
		$this->assertEquals($updatePayload['<?= $attribute->getName() ?>'], $updatedEntity-><?= $attribute->getGetterMethod($attribute->getName())?>->getId());
		<?php continue; endif; ?>
	<?php if ($attribute->isTypeDate()):?>
		//$this->assertEquals($updatePayload['<?= $attribute->getName() ?>'], $updatedEntity-><?= $attribute->getGetterMethod($attribute->getName())?>->format('Y-m-d H:i:s');
	<?php continue; endif; ?>
	$this->assertEquals($updatePayload['<?= $attribute->getName() ?>'], $updatedEntity-><?= $attribute->getGetterMethod($attribute->getName()) ?>);
<?php endforeach; ?>
}
}
