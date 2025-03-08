<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Shared\Application\Bus\AsyncMessage;
use App\Shared\Application\Bus\MessageBus;
use Symfony\Component\Messenger\MessageBusInterface;


class <?= $class_name ?> implements <?= $interface ?>
{
public function __construct(
private MessageBusInterface $messageBus
) {
}


public function dispatch(MessageAsync $message): void
{
$this->messageBus->dispatch($message);
}
}