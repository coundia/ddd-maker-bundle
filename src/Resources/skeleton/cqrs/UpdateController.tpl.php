<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use <?= $command_full_class ?>;
use App\Command\BaseCommandBus;
use App\Helpers\Response;
use Doctrine\ORM\EntityManagerInterface;

class <?= $class_name ?> extends AbstractController
{
private BaseCommandBus $commandBus;
private EntityManagerInterface $entityManager;

public function __construct(BaseCommandBus $commandBus, EntityManagerInterface $entityManager)
{
$this->commandBus = $commandBus;
$this->entityManager = $entityManager;
}

#[Route('<?= $route_path ?>/{id}', name: '<?= strtolower($entity_class_name) ?>_update', methods: ['PUT'])]
public function update(Request $request, int $id): JsonResponse
{
$data = json_decode($request->getContent(), true);
if (!is_array($data)) {
return Response::errorResponse('Invalid JSON data', 0, null, JsonResponse::HTTP_BAD_REQUEST);
}

// Inject the ID from the route into the data array
$data['id'] = $id;

<?php foreach ($attributes as $attr): ?>
	<?php if ($attr['type'] === 'DateTimeImmutable' || $attr['type'] === '\DateTimeImmutable'): ?>
        if (isset($data['<?= $attr['name'] ?>']) && is_string($data['<?= $attr['name'] ?>'])) {
        $data['<?= $attr['name'] ?>'] = new \DateTimeImmutable($data['<?= $attr['name'] ?>']);
        }
	<?php endif; ?>
<?php endforeach; ?>

<?php
$dtoParameters = '';
if (is_array($attributes)) {
	$dtoParameters = implode(', ', array_map(function($attr) {
		$attrName = is_array($attr) && isset($attr['name']) ? $attr['name'] : $attr;
		return "\$data['" . $attrName . "'] ?? null";
	}, $attributes));
}
?>
$dto = new <?= $dto_full_class ?>(<?= $dtoParameters ?>);

$command = <?= $command_full_class ?>::fromDto($dto);

try {
$entity = $this->entityManager->getConnection()->transactional(function () use ($command) {
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

return Response::successResponse($entity, JsonResponse::HTTP_OK, 'Entity updated successfully');
}
}
