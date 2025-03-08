<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;


interface <?= $class_name ?>
{
public function dispatch(object $message, array $stamps = []): mixed;
}
