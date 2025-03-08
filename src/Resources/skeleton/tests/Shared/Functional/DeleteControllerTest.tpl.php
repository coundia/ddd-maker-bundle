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
public function testDeleteEntity(): void
{
$entity = <?= $entity_factory ?>::createOne()->_disableAutoRefresh();

$response = $this->delete('/api/<?= $api_version ?>/<?= $entity_name_plural ?>/' . $entity->getId());
$response->assertResponseStatusCodeSame(Response::HTTP_NO_CONTENT);
}

#[\PHPUnit\Framework\Attributes\Test]
public function testDeleteNonExistentEntity(): void
{
$this->delete('/api/<?= $api_version ?>/<?= $entity_name_plural ?>/-1')
->assertResponseStatusCodeSame(Response::HTTP_NOT_FOUND);
}
}
