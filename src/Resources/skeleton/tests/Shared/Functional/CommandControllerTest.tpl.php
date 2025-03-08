<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $base_test_case ?>;
use Zenstruck\Foundry\Test\Factories;
use Symfony\Component\HttpFoundation\Response;

use function Zenstruck\Foundry\faker;

class <?= $class_name ?> extends <?= $base_test_case ?>

{

#[\PHPUnit\Framework\Attributes\Test]
public function testCreateEntity(): void
{

$payload = [
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>
	'<?= $attribute->getName() ?>' => <?= $attribute->generateFakerValue($attribute->getType()) ?>,
<?php endforeach; ?>
];

$response = $this->post('/api/<?= $api_version ?>/<?= $entity_name_plural ?>', $payload);
$response->assertResponseStatusCodeSame(Response::HTTP_CREATED);

$response->assertResponseStatusCodeSame(Response::HTTP_CREATED);
$content = $response->getData();

$this->assertArrayHasKey('id', $content);
$this->assertNotNull($content['id']);
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->isId()) continue; ?>

	$this->assertEquals($payload['<?= $attribute->getName() ?>'], $content['<?= $attribute->getName() ?>']);
<?php endforeach; ?>
}

}