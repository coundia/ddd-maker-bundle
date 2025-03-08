<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use <?= $command_full_class ?>;
use App\Command\BaseCommandBus;
use Doctrine\ORM\EntityManagerInterface;
use App\Helpers\Response;

class <?= $class_name ?> extends AbstractController
{
private BaseCommandBus $commandBus;
private EntityManagerInterface $entityManager;

public function __construct(BaseCommandBus $commandBus, EntityManagerInterface $entityManager)
{
$this->commandBus = $commandBus;
$this->entityManager = $entityManager;
}

#[Route('<?= $route_path ?>/bulk', name: '<?= strtolower($entity_class_name) ?>_bulk_create', methods: ['POST'])]
public function bulkCreate(Request $request): JsonResponse
{
$data = json_decode($request->getContent(), true);
if (!is_array($data)) {
return $this->json(['error' => 'Invalid data format, expected an array'], 400);
}
$command = new <?= $command_class_name ?>(data: $data);
try {
$result = $this->entityManager->getConnection()->transactional(function () use ($command) {
return $this->commandBus->dispatch($command);
});
} catch (\Throwable $e) {
return Response::errorResponse(
$e->getMessage(),
(int)$e->getCode(),
['exception' => $e->getTraceAsString()],
JsonResponse::HTTP_INTERNAL_SERVER_ERROR
);
}
return Response::successResponse($result, JsonResponse::HTTP_CREATED, 'Products created successfully');
}
}
