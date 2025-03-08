<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use App\Security\Domain\Response\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/home', name: 'api_home', methods: ['GET'])]
class <?= $class_name ?> extends AbstractController
{
public function __invoke(): JsonResponse
{
return Response::successResponse([
'message' => 'Welcome to the DDD Maker Bundle',
]);
}
}
