<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use Symfony\Component\HttpFoundation\JsonResponse;

class <?= $class_name ?>
{
/**
* Generate a success response.
*
* @param mixed $data The payload to include in the response.
* @param int $status The HTTP status code (default is 200).
* @param string $message A message to include in the response.
* @return JsonResponse
*/
public static function successResponse(mixed $data, int $status = JsonResponse::HTTP_OK, string $message = 'Success'): JsonResponse
{
return new JsonResponse([
'success' => true,
'data'    => $data,
'message' => $message,
], $status);
}

/**
* Generate an error response.
*
* @param string $message The error message.
* @param int $code A specific application-level error code.
* @param array|null $details Additional error details (optional).
* @param int $status The HTTP status code (default is 400).
* @return JsonResponse
*/
public static function errorResponse(string $message = "Error", int $code = 0, ?array $details = null, int $status = JsonResponse::HTTP_BAD_REQUEST): JsonResponse
{
$response = [
'success' => false,
'message' => $message,
'code'    => $code,
];

if ($details) {
$response['errors'] = $details;
}

return new JsonResponse($response, $status);
}
}
