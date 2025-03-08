<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
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

#[Route('<?= $route_path ?>', name: '<?= strtolower($entity_class_name) ?>_list', methods: ['GET'])]
public function list(Request $request): JsonResponse
{
$page = (int) $request->query->get('page', 1);
$limit = (int) $request->query->get('limit', 10);

// Extract filters from query parameters, excluding pagination parameters.
$filters = $request->query->all();
unset($filters['page'], $filters['limit']);

$query = new <?= $query_class_name ?>(page: $page, limit: $limit, filters: $filters);
$result = $this->queryBus->dispatch($query);

$total = $result['total'] ?? 0;
$totalPages = $limit > 0 ? (int) ceil($total / $limit) : 0;

$responseData = [
'data' => $result['data'] ?? [],
'meta' => [
'page'       => $page,
'limit'      => $limit,
'total'      => $total,
'totalPages' => $totalPages,
],
];

return Response::successResponse($responseData, JsonResponse::HTTP_OK, 'Records fetched successfully');
}
}
