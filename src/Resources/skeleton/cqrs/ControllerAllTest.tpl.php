<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Tests\BaseApiTestCase;
use <?= $entity_full_class ?>;

class <?= $class_name ?> extends BaseApiTestCase
{
public function testList<?= $entity_class_name ?>WithFilter(): void
{
$entityManager = self::getContainer()->get('doctrine')->getManager();
$uniq = uniqid('Test-');
$<?= strtolower($entity) ?> = new <?= $entity ?>(
<?php foreach ($attributes as $attribute): ?>
	<?= $attribute->getName() ?>: <?php
	if ($attribute->getType() === 'string'):
		echo '$uniq';
    elseif ($attribute->getType() === 'float'):
		echo 'random_int(100, 999)';
    elseif ($attribute->getType() === 'int'):
		echo 'random_int(1000, 9999)';
    elseif ($attribute->getType() === '\DateTimeImmutable' || $attribute->getType() === 'DateTimeImmutable'):
		echo "new \\DateTimeImmutable('2020-01-01 00:00:00')";
	else:
		echo 'null';
	endif;
	?>,
<?php endforeach; ?>
);

$entityManager->persist($<?= strtolower($entity) ?>);
$entityManager->flush();

$filterField = '<?= $attributes[0]['name'] ?>';
$response = $this->request('GET', '/api/<?= strtolower($entity) ?>?page=1&limit=10&' . $filterField . '=' . $uniq);
$response->assertResponseStatusCodeSame(200);
$data = $response->getData();
$this->assertArrayHasKey('meta', $data);
$this->assertGreaterThan(0, $data['meta']['total'], 'Total count should be greater than 0');
$this->assertNotEmpty($data['data'], 'Data should not be empty');
}
}
