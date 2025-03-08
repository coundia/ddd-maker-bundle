<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Shared\Domain\DTO\ApiResponseDTO;
use App\Shared\Domain\DTO\ErrorResponseDTO;

use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;

use <?= $entity_full_class_id ?>;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Response\Response;


#[Route('<?= $route_path ?>', name: '<?= $route_name ?>', methods: ['PUT'])]
#[OA\Put(
path: '<?= $route_path ?>',
summary: 'Updates an existing <?= $entity_class_name ?> item.',
requestBody: new OA\RequestBody(
description: 'Data required to update a <?= $entity_class_name ?>.',
required: true,
content: new OA\JsonContent(ref: new Model(type: <?= $entity_class_name ?>RequestDTO::class, groups: ['default']))
),
tags: ['<?= $entity_class_name ?>s'],
parameters: [
new OA\Parameter(
name: 'id',
description: 'ID of the <?= $entity_class_name ?> item to update.',
in: 'path',
required: true,
schema: new OA\Schema(type: 'integer', example: 1)
)
],
responses: [
new OA\Response(
response: 200,
description: '<?= $entity_class_name ?> updated successfully.',
content: new OA\JsonContent(ref: new Model(type: ApiResponseDTO::class, groups: ['default']))
),
new OA\Response(
response: 400,
description: 'Invalid input.',
content: new OA\JsonContent(ref: new Model(type: ErrorResponseDTO::class, groups: ['error']))
)
]
)]
class <?= $class_name ?> extends <?= $parent_class_name ?>
{
public function __construct(
private <?= $query_service ?> $query_service,
private <?= $update_service ?> $update_service,
private \App\<?= $context ?>\Application\Mapper\<?= $entity_class_name ?>\<?= $entity_class_name ?>MapperInterface $mapper
) {
}

public function __invoke(mixed $id, Request $request): JsonResponse
{
$item = $this->query_service->find(<?= $entity_class_name ?>Id::create(id: $id));
if (!$item) {
return Response::errorResponse('<?= $entity_class_name ?> not found', 4004,null,Response::HTTP_NOT_FOUND);
}

try {
$data = json_decode($request->getContent(), true);

$dto = $this->mapper->fromArray($data);
$model = $this->update_service->update($dto, <?= $entity_class_name ?>Id::create($id));
$responseDTO = $this->mapper->toArray($model);

return Response::successResponse($responseDTO, 200, '<?= $entity_class_name ?> updated successfully.');
} catch (\Exception $e) {
return Response::errorResponse($e->getMessage(), 400);
}
}
}
