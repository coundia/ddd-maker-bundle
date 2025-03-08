<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use PHPUnit\Framework\Assert;
use Symfony\Contracts\HttpClient\ResponseInterface;

final class <?= $class_name ?> implements ResponseInterface
{
private ResponseInterface $_response;

public function __construct(ResponseInterface $response)
{
$this->_response = $response;
}

public function getStatusCode(): int
{
return $this->_response->getStatusCode();
}

public function getHeaders(bool $throw = false): array
{
return $this->_response->getHeaders($throw);
}

public function getContent(bool $throw = false): string
{
return $this->_response->getContent($throw);
}

public function toArray(bool $throw = false): array
{
return $this->_response->toArray($throw);
}

public function cancel(): void
{
$this->_response->cancel();
}

public function getInfo(?string $type = null): mixed
{
return $this->_response->getInfo($type);
}

public function getData(?string $params = null): mixed
{
$responseData = $this->toArray();
$data = $responseData['data'] ?? null;
if ($params && is_array($data)) {
$data = $data[$params] ?? $data;
}
return $data;
}

public function assertResponse(bool $isOk = true): self
{
$success = $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
Assert::assertEquals(
$isOk,
$success,
sprintf('Failed asserting that the response is %s. Got status code %d.', $isOk ? 'successful' : 'not successful', $this->getStatusCode())
);
return $this;
}

public function assertResponseStatusCodeSame(int $expectedStatusCode): self
{
Assert::assertSame(
$expectedStatusCode,
$this->getStatusCode(),
sprintf('Failed asserting that the response status code is %d.', $expectedStatusCode)
);
return $this;
}

public function assertResponseIsSuccessful(bool $isOk = true): self
{
$this->assertResponse($isOk);
$success_key = $this->toArray()['success'] ?? null;
Assert::assertEquals(
$isOk,
$success_key,
sprintf('Failed asserting that the response is %s. Got success key %s.', $isOk ? 'successful' : 'not successful', $success_key)
);
return $this;
}

public function assertMessage(string $message): self
{
$messageOK = $this->toArray()['message'] ?? null;
Assert::assertEquals(
$message,
$messageOK,
sprintf('Failed asserting that the response message is %s. Got message %s.', $message, $messageOK)
);
return $this;
}

public function assertIsErrors(): void
{
$this->assertResponseIsSuccessful(false);
}

public function assertResponseData(array $dataAttributes): self
{
$this->assertResponseIsSuccessful();
$responseData = $this->toArray();
$data = $responseData['data'] ?? null;

foreach ($dataAttributes as $key => $value) {
Assert::assertArrayHasKey($key, $data);
Assert::assertEquals($value, $data[$key]);
}
return $this;
}

public function assertResponseErrors(int $statusCode, int $statusInternCode, array $dataErrorAttributes): self
{
$this->assertResponse(false);
$responseData = $this->toArray();
$errorsData = $responseData['errors'] ?? null;
$statusCodeIntern = $responseData['code'] ?? null;
$this->assertResponseStatusCodeSame($statusCode);
Assert::assertEquals($statusInternCode, $statusCodeIntern, sprintf('Failed asserting that the response status code is %d.', $statusInternCode));

foreach ($dataErrorAttributes as $key => $value) {
Assert::assertArrayHasKey($key, $errorsData);
Assert::assertEquals($value, $errorsData[$key]);
}
return $this;
}

public function debug()
{
var_dump($this->toArray());
return $this;
}
}
