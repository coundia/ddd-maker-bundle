<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/**
* ResponseAssert.
* Provides assertion methods for validating API responses.
*/
class <?= $class_name ?>
{
public static function assertSuccess(array $response): void
{
if (!$response['success']) {
throw new \Exception('Response is not successful');
}
}
}
