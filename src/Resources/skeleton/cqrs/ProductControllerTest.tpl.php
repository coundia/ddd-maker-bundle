<?= "<?php\n" ?>
declare(strict_types=1);

namespace App\Tests\Controller;
 
use App\Tests\BaseApiTestCase;

class ProductControllerTest extends BaseApiTestCase
{
public function testGetProductNotFound(): void
{
 
$this->request('GET', '/product/99999');
$this->assertEquals(404, $this->getResponse()->getStatusCode());
}

public function testAddProduct(): void
{

$data = [
'name'  => 'Test Product',
'price' => 99.99,
];

$response = $this->request(
'POST',
'/product',
$data
);
$this->assertEquals(201, $this->getResponse()->getStatusCode());
$responseData = $response->getData();
$this->assertArrayHasKey('id', $responseData);
$this->assertEquals('Test Product', $responseData['name']);
$this->assertEquals(99.99, $responseData['price']);
}
}
