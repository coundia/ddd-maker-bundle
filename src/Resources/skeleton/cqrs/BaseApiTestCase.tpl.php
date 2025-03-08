<?= "<?php\n" ?>
declare(strict_types=1);

namespace <?= $namespace ?>;

use ApiPlatform\Symfony\Bundle\Test\ApiTestCase;
use ApiPlatform\Symfony\Bundle\Test\Client;
use <?= $user_entity_full_class ?>;
use App\Helpers\ResponseType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Contracts\HttpClient\ResponseInterface;
use Symfony\Contracts\HttpClient\ResponseStreamInterface;

abstract class <?= $class_name ?> extends ApiTestCase implements HttpClientInterface
{
private ?Client $client = null;
protected const DEFAULT_USER_EMAIL = 'testuser@example.com';
protected const DEFAULT_USER_PASSWORD = 'password123';
private array $data;
/**
* @var mixed|string
*/
private mixed $dataName;

public function __construct(?string $name = null, array $data = [], $dataName = '')
{
if ($name !== null) {
$this->setName($name);
}

$this->data = $data;
$this->dataName = $dataName;
$this->client = static::createClient();

parent::__construct($name, $data, $dataName);
}

protected function createTestUser(string $email = self::DEFAULT_USER_EMAIL, string $password = self::DEFAULT_USER_PASSWORD): User
{
$client = $this->client();
$passwordHasher = $client->getContainer()->get(UserPasswordHasherInterface::class);

$user = $client->getContainer()->get('doctrine')->getRepository(User::class)->findOneBy(['email' => $email]);
if ($user) {
return $user;
}

$user = new User();
$user->setEmail($email);
$user->setPassword($passwordHasher->hashPassword($user, $password));
$user->setRoles(['ROLE_USER']);

$em = $client->getContainer()->get('doctrine')->getManager();
$em->persist($user);
$em->flush();

return $user;
}

protected function loginAndGetToken(string $email = self::DEFAULT_USER_EMAIL, string $password = self::DEFAULT_USER_PASSWORD): string
{
$response = $this->client()->request('POST', '/api/login', [
'json' => [
'email' => $email,
'password' => $password,
],
]);

$this->assertResponseIsSuccessful();
$responseData = $this->getResponseData($response);

$this->assertArrayHasKey('data', $responseData);
$this->assertArrayHasKey('token', $responseData['data']);

return $responseData['data']['token'];
}

protected function getResponseData($response): array
{
return json_decode($response->getContent(false), true);
}

public function client(): Client
{
if ($this->client === null) {
$this->client = static::createClient();
}
return $this->client;
}

public function request(string $method, string $url, array $options = []): ResponseType
{
$this->createTestUser();
$token = $this->loginAndGetToken();
$payload['headers'] = $payload['headers'] ?? [];
$payload['headers']['Authorization'] = 'Bearer ' . $token;
$payload['json'] = $options;

$response = $this->client()->request($method, $url, $payload);

return new ResponseType($response);
}

public function requestWithoutToken(string $method, string $uri, array $payload = []): ResponseType
{
$response = $this->client()->request($method, $uri, $payload);
return new ResponseType($response);
}

public function getTokenForUser(User $user): string
{
return static::getContainer()->get('lexik_jwt_authentication.jwt_manager')->create($user);
}

public function getEntityManagement()
{
return self::getContainer()->get('doctrine')->getManager();
}

public function assertSameDate($expected, $actual): void
{
if (is_array($expected)) {
$expected = $expected['date'];
}
if (is_array($actual)) {
$actual = $actual['date'];
}
$expectedDate = new \DateTime($expected);
$actualDate = new \DateTime($actual);
$this->assertEquals($expectedDate->format('Y-m-d'), $actualDate->format('Y-m-d'));
}

public function executeSql(string $sql, array $params = []): int
{
$entityManager = self::getContainer()->get(EntityManagerInterface::class);
return $entityManager->getConnection()->executeStatement($sql, $params);
}

public function stream(iterable|ResponseInterface $responses, ?float $timeout = null): ResponseStreamInterface
{
return $this->client->stream($responses, $timeout);
}

public function withOptions(array $options): static
{
$this->client->withOptions($options);
return $this;
}
}
