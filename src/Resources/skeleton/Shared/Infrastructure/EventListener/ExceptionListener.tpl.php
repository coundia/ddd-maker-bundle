<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Psr\Log\LoggerInterface;

class <?= $class_name ?> implements <?= $interface ?>
{
public function __construct(private LoggerInterface $logger) {}

public function onKernelException(ExceptionEvent $event): void
{
$exception = $event->getThrowable();
$this->logger->error($exception->getMessage(), ['exception' => $exception]);

$response = new JsonResponse([
'success' => false,
'code' => $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500,
'message' => $exception->getMessage(),
'data' => [get_class($exception)],
], $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : 500);

$event->setResponse($response);
}
}
