<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Shared\Domain\Response\Response;
use <?= $DTONamespace ?>RequestDTO;
use <?= $DTONamespace ?>ResponseDTO;

use <?= $entity_full_class_id ?>;

use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

#[Route('<?= $route_path ?>', name: '<?= strtolower($route_name) ?>', methods: ['POST'])]
#[OA\Post(
path: '<?= $route_path ?>',
summary: 'Creates or updates <?= $entity_class_name ?> items in bulk.',
requestBody: new OA\RequestBody(
description: 'An array of <?= $entity_class_name ?> items to create or update.',
required: true,
content: new OA\JsonContent(
type: 'array',
items: new OA\Items(ref: new Model(type: <?= $entity_class_name ?>RequestDTO::class, groups: ['default']))
)
),
tags: ['<?= $entity_class_name ?>s'],
responses: [
new OA\Response(
response: 201,
description: '<?= $entity_class_name ?>s processed successfully.',
content: new OA\JsonContent(
properties: [
new OA\Property(property: 'success', type: 'boolean', example: true),
new OA\Property(
property: 'data',
type: 'array',
items: new OA\Items(ref: new Model(type: <?= $entity_class_name ?>ResponseDTO::class, groups: ['default']))
),
new OA\Property(property: 'message', type: 'string', example: '<?= $entity_class_name ?>s processed successfully.'),
]
)
),
new OA\Response(
response: 400,
description: 'Invalid input.',
content: new OA\JsonContent(
properties: [
new OA\Property(property: 'success', type: 'boolean', example: false),
new OA\Property(property: 'errors', type: 'array', items: new OA\Items(type: 'string')),
new OA\Property(property: 'message', type: 'string', example: 'Some <?= $entity_class_name ?>s failed to process.'),
]
)
),
]
)]
class <?= $class_name ?> extends <?= $parent_class_name ?>
{
public function __construct(
private readonly <?= $create_service ?> $create_service,
private readonly <?= $query_service ?> $query_service,
private readonly <?= $update_service ?> $update_service,
private \App\<?= $context ?>\Application\Mapper\<?= $entity_class_name ?>\<?= $entity_class_name ?>MapperInterface $mapper
) {}

public function __invoke(Request $request): JsonResponse
{
$data = json_decode($request->getContent(), true);

if (!is_array($data)) {
return Response::errorResponse('Input must be an array of <?= $entity_class_name ?>s.', 400);
}

$results = [];
$errors = [];

foreach ($data as $index => $itemData) {
try {
if (!is_array($itemData)) {
throw new \InvalidArgumentException("Invalid data format at index $index.");
}

$requestDTO = $this->mapper->fromArray($itemData);
$id = $itemData['id'] ?? null;

if ($id) {
$existingItem = $this->query_service->find($existingItemId = <?= $entity_class_name ?>Id::create(id: $id));
if ($existingItem) {
$model = $this->update_service->update($existingItem, $existingItemId);
$results[] = $this->mapper->toArray($model);
continue;
}
}

$model = $this->create_service->create($requestDTO);

$results[] = $this->mapper->toArray($model);

} catch (\Exception $e) {
$errors[$index] = $e->getMessage();
}
}

if (!empty($errors)) {
return Response::errorResponse(json_encode(['errors' => $errors]), 400,['errors' => $errors]);
}

return Response::successResponse($results, Response::HTTP_CREATED, '<?= $entity_class_name ?>s processed successfully.');
}
}
