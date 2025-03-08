<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Tests\BaseApiTestCase;
use <?= $entity_full_class ?>;

class <?= $class_name ?> extends BaseApiTestCase
{
public function testUpdate<?= $entity ?>(): void
{
$entityManager = self::getContainer()->get('doctrine')->getManager();
$uniq = uniqid('Test-');
$<?= strtolower($entity) ?> = new <?= $entity ?>(
<?php foreach ($attributes as $attribute): ?>
	<?= $attribute->getName() ?>: <?php
	if ($attribute->getType() === 'string'):
		echo '$uniq';
    elseif ($attribute->getType() === 'float'):
		echo 'random_int(100, 999) / 10';
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

$id = $<?= strtolower($entity) ?>->id;

// Prepare updated data array. Use new values for each field.
$updatedData = [
<?php foreach ($attributes as $attribute): ?>
	<?php if ($attribute->getName() !== 'id'): ?>
        '<?= $attribute->getName() ?>' => <?php
		if ($attribute->getType() === 'string'):
			echo 'uniqid("Updated-")';
        elseif ($attribute->getType() === 'float'):
			echo 'random_int(100, 999) / 10';
        elseif ($attribute->getType() === 'int'):
			echo 'random_int(1000, 9999)';
        elseif ($attribute->getType() === '\DateTimeImmutable' || $attribute->getType() === 'DateTimeImmutable'):
			echo "(new \\DateTimeImmutable('2021-01-01 00:00:00'))->format(\\DateTimeInterface::ATOM)";
		else:
			echo 'null';
		endif;
		?>,
	<?php endif; ?>
<?php endforeach; ?>
'id' => $id,
];

// Send update request using BaseApiTestCase's request method.
$response = $this->request('PUT', "/api/<?= strtolower($entity) ?>/{$id}", $updatedData);
$response->assertResponseStatusCodeSame(200);

$responseData = $response->getData();

// Verify that the first attribute (except 'id') has been updated.
$expectedUpdatedValue = $updatedData['<?= $attributes[0]['name'] ?>'] ?? null;
$this->assertEquals($expectedUpdatedValue, $responseData['<?= $attributes[0]['name'] ?>'], 'Mismatch in <?= $attributes[0]['name'] ?> field.');
}
}
