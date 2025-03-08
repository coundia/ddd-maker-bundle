<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Tests\BaseApiTestCase;

class <?= $class_name ?> extends BaseApiTestCase
{
public function testBulkCreate<?= $entity_class_name ?>(): void
{
$data = [];
$record = [];
<?php foreach ($attributes as $attribute): ?>
    $record['<?= $attribute->getName() ?>'] = <?php
	if ($attribute->getType() === 'string'):
		?>uniqid("Test-")<?php
    elseif ($attribute->getType() === 'int'):
		?>random_int(1000, 9999)<?php
    elseif ($attribute->getType() === 'float'):
		?>random_int(100, 999) / 10<?php
    elseif ($attribute->getType() === '\DateTimeImmutable' || $attribute->getType() === 'DateTimeImmutable'):
		?>(new \DateTimeImmutable('2020-01-01 00:00:00'))->format(\DateTimeInterface::ATOM)<?php
	else:
		?>null<?php
	endif;
	?>;
<?php endforeach; ?>
$data[] = $record;

$response = $this->request('POST', '/api/<?= strtolower($entity) ?>/bulk', $data);
$response->assertMessage('Products created successfully');
$response->assertResponseIsSuccessful();

$result = $response->getData();
$this->assertIsArray($result, 'Response data should be an array');
$this->assertNotEmpty($result, 'Response data should not be empty');

foreach ($result as $item) {
<?php foreach ($attributes as $attribute): ?>
    $this->assertArrayHasKey('<?= $attribute->getName() ?>', $item, 'Product should have <?= $attribute->getName() ?>');
<?php endforeach; ?>
}
}
}
