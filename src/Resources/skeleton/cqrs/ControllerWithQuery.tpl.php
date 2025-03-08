<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use <?= $query_full_class ?>;
use App\Query\BaseQueryBus;
use App\Helpers\Response;

class <?= $class_name ?> extends AbstractController
{
private BaseQueryBus $queryBus;

public function __construct(BaseQueryBus $queryBus)
{
$this->queryBus = $queryBus;
}

#[Route('<?= $route_path ?>/{id}', name: '<?= strtolower($entity) ?>_show', methods: ['GET'])]
public function show(int $id): JsonResponse
{
$query = new <?= $query_class_name ?>( $id );
$entity = $this->queryBus->dispatch($query);
if (!$entity) {
return Response::errorResponse('Entity not found', 0, null, JsonResponse::HTTP_NOT_FOUND);
}
return Response::successResponse($entity, JsonResponse::HTTP_OK, 'Entity found');
}
}
