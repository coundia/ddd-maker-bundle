<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;
use App\Shared\Domain\DTO\ApiResponseDTO;
use App\Shared\Domain\DTO\ErrorResponseDTO;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use App\Shared\Domain\Response\Response;

use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;

#[Route('<?= $route_path ?>', name: '<?= $route_name ?>', methods: ['GET'])]
#[OA\Get(
path: '<?= $route_path ?>',
summary: 'Retrieves <?= $entity_class_name ?> by <?=lcfirst($parameter)?>.',
tags: ['<?= $entity_class_name ?>s'],
parameters: [
new OA\Parameter(
name: '<?=lcfirst($parameter)?>',
description: 'Parameter <?=lcfirst($parameter)?>.',
in: 'query',
required: true,
schema: new OA\Schema(type: 'string', default: 1)
)
],
responses: [
new OA\Response(
response: 200,
description: 'Find  <?= $entity_class_name ?> by <?=ucfirst($parameter)?> successfully.',
content: new OA\JsonContent(
properties: [
new OA\Property(property: 'success', type: 'boolean', example: true),
new OA\Property(
property: 'data',
properties: [
new OA\Property(property: 'items', type: 'array', items: new OA\Items(new Model(type: <?= $entity_class_name ?>RequestDTO::class, groups: ['default']))),
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
public function __construct(
private <?= $query_interface ?> $query_bus,
private \App\<?= $context ?>\Application\Mapper\<?= $entity_class_name ?>\<?= $entity_class_name ?>MapperInterface $mapper
)
{
}

public function __invoke(Request $request): JsonResponse
{
try {

$parameter = $request->query->get('<?=lcfirst($parameter)?>',null);

if(!$parameter){
	return Response::errorResponse('<?=lcfirst($parameter)?> is required in query', 400);
}

$query = new <?= $query_name ?>(
<?php foreach ($attributes as $attribute): ?>
<?php if ($attribute->getName() == lcfirst($parameter)): ?>
	<?php if ($attribute->isValueObject()): ?><?= $attribute->getName() ?>: <?= $attribute->getObjectValue($attribute->getName()) ?>::create($parameter),
		<?php continue; endif; ?> <?= $attribute->getName() ?>: $parameter,
<?php endif; ?>
<?php endforeach; ?>);

$response = $this->query_bus->dispatch($query);

return Response::successResponse(
[
'items' => $response
]
);
} catch (\Exception $e) {
return Response::errorResponse($e->getMessage(), 400);
}
}
}
