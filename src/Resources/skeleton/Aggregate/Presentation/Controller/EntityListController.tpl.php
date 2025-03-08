<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;


use App\Shared\Domain\DTO\ApiResponseDTO;
use App\Shared\Domain\DTO\ErrorResponseDTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Response\Response;


#[Route('<?= $route_path ?>', name: '<?= $route_name ?>', methods: ['GET'])]
#[OA\Get(
path: '<?= $route_path ?>',
summary: 'Retrieves all <?= $entity_class_name ?> items with pagination.',
tags: ['<?= $entity_class_name ?>s'],
parameters: [
new OA\Parameter(
name: 'page',
description: 'Page number.',
in: 'query',
required: false,
schema: new OA\Schema(type: 'integer', default: 1)
),
new OA\Parameter(
name: 'limit',
description: 'Number of items per page.',
in: 'query',
required: false,
schema: new OA\Schema(type: 'integer', default: 10)
)
],
responses: [
new OA\Response(
response: 200,
description: 'List of <?= $entity_class_name ?> items retrieved successfully.',
content: new OA\JsonContent(
properties: [
new OA\Property(property: 'success', type: 'boolean', example: true),
new OA\Property(
property: 'data',
properties: [
new OA\Property(property: 'items', type: 'array', items: new OA\Items(new Model(type: <?= $entity_class_name ?>RequestDTO::class, groups: ['default']))),
new OA\Property(property: 'total', type: 'integer', example: 100),
new OA\Property(property: 'page', type: 'integer', example: 1),
new OA\Property(property: 'limit', type: 'integer', example: 10)
],
type: 'object'
),
new OA\Property(property: 'message', type: 'string', example: '<?= $entity_class_name ?>s retrieved successfully.')
]
)
),
new OA\Response(
response: 400,
description: 'An error occurred while retrieving <?= $entity_class_name ?>s.',
content: new OA\JsonContent(
properties: [
new OA\Property(property: 'success', type: 'boolean', example: false),
new OA\Property(property: 'message', type: 'string', example: 'Error message')
]
)
)
]
)]
class <?= $class_name ?> extends <?= $parent_class_name ?>
{
public function __construct(private <?= $query_service ?> $query_service)
{
}

public function __invoke(Request $request): JsonResponse
{
try {
$page = $request->query->getInt('page', 1);
$limit = $request->query->getInt('limit', 10);

$paginated = $this->query_service->findPaginated($page, $limit, []);

return Response::successResponse($paginated);
} catch (\Exception $e) {
return Response::errorResponse($e->getMessage(), 400);
}
}
}
