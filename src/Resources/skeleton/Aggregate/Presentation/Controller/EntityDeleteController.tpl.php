<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Shared\Domain\DTO\ApiResponseDTO;
use App\Shared\Domain\DTO\ErrorResponseDTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Response\Response;
use <?= $entity_full_class_id ?>;

#[Route('<?= $route_path ?>', name: '<?= $route_name ?>', methods: ['DELETE'])]
#[OA\Delete(
path: '<?= $route_path ?>',
summary: 'Deletes a <?= $entity_class_name ?> item.',
tags: ['<?= $entity_class_name ?>s'],
parameters: [
new OA\Parameter(
name: 'id',
description: 'ID of the <?= $entity_class_name ?> item to delete.',
in: 'path',
required: true,
schema: new OA\Schema(type: 'integer', example: 1)
)
],
responses: [
new OA\Response(
response: 200,
description: '<?= $entity_class_name ?> deleted successfully.',
content: new Model(type: ApiResponseDTO::class, groups: ['default'])
),
new OA\Response(
response: 404,
description: '<?= $entity_class_name ?> not found.',
content: new Model(type: ErrorResponseDTO::class, groups: ['error'])
)
]
)]
class <?= $class_name ?> extends <?= $parent_class_name ?>
{
public function __construct(
private <?= $delete_service ?> $delete_service,
private <?= $query_service ?> $query_service
)
{
}

public function __invoke(mixed $id): JsonResponse
{
try {

$item = $this->query_service->find(<?= $entity_class_name ?>Id::create(id: $id));
if (!$item) {
return Response::errorResponse('<?= $entity_class_name ?> not found', 4004,null,Response::HTTP_NOT_FOUND);
}

$response = $this->delete_service->delete(<?= $entity_class_name ?>Id::create(id: $id));
return Response::successResponse($response?->value(), Response::HTTP_NO_CONTENT, "<?= $entity_class_name ?> deleted successfully.");

} catch (\Exception $e) {
return Response::errorResponse($e->getMessage(), 400);
}
}
}