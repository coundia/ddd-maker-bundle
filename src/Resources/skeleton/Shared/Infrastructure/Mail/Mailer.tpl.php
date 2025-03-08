<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;


class <?= $class_name ?> implements <?= $interface ?>
{
public function send(mixed $message): void{

  throw new \Exception('Not implemented ');
}
}