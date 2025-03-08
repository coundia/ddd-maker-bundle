<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

/** Exception for <?= $entity_class_name ?> domain errors */
class <?= $class_name ?> extends \Exception
{
public function __construct(string $message = "An error occurred in <?= $entity_class_name ?> domain")
{
parent::__construct($message);
}

public static function because(string $reason): self
{
return new self($reason);
}
}
