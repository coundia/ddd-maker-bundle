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


#[Route('<?= $route_path ?>', name: '<?= strtolower($route_name) ?>', methods: ['POST'])]
#[OA\Post(
path: '<?= $route_path ?>',
summary: 'Creates a new <?= $entity_class_name ?> item.',
requestBody: new OA\RequestBody(
description: 'Data required to create a <?= $entity_class_name ?>.',
required: true,
content: new Model(type: <?= $entity_class_name ?>RequestDTO::class, groups: ['default'])
),
tags: ['<?= $entity_class_name ?>s'],
responses: [
new OA\Response(
response: 201,
description: '<?= $entity_class_name ?> created successfully.',
content: new Model(type: ApiResponseDTO::class, groups: ['default'])
),
new OA\Response(
response: 400,
description: 'Invalid input.',
content: new Model(type: ErrorResponseDTO::class, groups: ['error'])
)
]
)]
class <?= $class_name ?> extends <?= $parent_class_name ?>
{
public function __construct(
private <?= $create_service ?> $creation_service,
private \App\<?= $context ?>\Application\Mapper\<?= $entity_class_name ?>\<?= $entity_class_name ?>MapperInterface $mapper
) {
}

public function __invoke(Request $request): JsonResponse
{
$data = json_decode($request->getContent(), true);

try {

$dto = $this->mapper->fromArray($data);
$model = $this->creation_service->create($dto);
$responseDTO = $this->mapper->toArray($model);

return Response::successResponse($responseDTO,Response::HTTP_CREATED);
} catch (\Exception $e) {
return Response::errorResponse($e->getMessage());
}
}
}
