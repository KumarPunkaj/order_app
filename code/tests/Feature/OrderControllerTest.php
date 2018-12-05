<?php

namespace App\Test\Feature\ApiController;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderControllerTest extends TestCase
{
    use WithoutMiddleware;

    public function testOrderCreateIncorrectParameters()
    {
        echo "\n *** Executing Integration Test *** \n";

        echo "\n *** Executing Order Create Scenarion (Negative and Positive) *** \n";

        echo "\n > Order Create Negative Test - With Invalid Parameter Keys \n";
        $invalidData1 = [
            'origin1' => ['44.968046', '-94.420307'],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $response = $this->json('POST', '/orders', $invalidData1);

        $response->assertStatus(422);
    }

    public function testOrderCreateEmptyParameters()
    {
        echo "\n > Order Create Negative Test - With Empty Parameter \n";
        $invalidData1 = [
            'origin' => ['44.968046', ''],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $response = $this->json('POST', '/orders', $invalidData1);

        $response->assertStatus(422);
    }

    public function testOrderCreateInvalidData()
    {
        echo "\n > Order Create Negative Test - Invalid Data \n";
        $invalidData1 = [
            'origin' => ['44.968046', 'test', '44.968046'],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $response = $this->json('POST', '/orders', $invalidData1);

        $response->assertStatus(422);
    }

    public function testOrderCreationPositiveScenario()
    {
        echo "\n > Order Create Positive Test - Valid Data \n";

        $validData = [
            'origin' => ['44.968046', '-94.420307'],
            'destination' => [
                '44.33328',
                '-29.132008',
            ],
        ];

        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();

        $response->assertStatus(200);

        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('distance', $data);
    }

    public function testOrderUpdate()
    {
        echo "\n \n \n*** Executing Order Update Scenarion (Positive and Negative) *** \n";

        echo "\n > Order Update Positive Test - Valid Data \n";

        echo "\n \t > Creating an order \n";
        $validData = [
            'origin' => ['44.968046', '-94.420307'],
            'destination' => [
                '44.33328',
                '-29.132008',
            ],
        ];

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();
        $orderId = $data['id'];

        echo "\n \t > Updating Order \n";
        $response = $this->json('PATCH', '/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        $response->assertStatus(200);
        $this->assertArrayHasKey('success', $data);

        echo "\n > Order Update Negative Test - For Alread updated order \n";
        echo "\n \t > Trying to update same order - should get error \n";

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('PATCH', '/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        $response->assertStatus(409);
        $this->assertArrayHasKey('error', $data);

        echo "\n > Order Update Negative Test - Invalid Params key (status1) \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status1' => 'TAKEN'], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Invalid Param value (TAKEN1) \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status' => 'TAKEN1'], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Empty Param value \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status' => ''], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Invalid Order id \n";
        $this->orderUpdateFailureInvalidParams(9999999, ['status' => 'TAKEN'], $expectedCode = 417);
    }

    protected function orderUpdateFailureInvalidParams($orderId, $params, $expectedCode)
    {
        $response = $this->json('PATCH', '/orders/'. $orderId, $params);
        $data = (array) $response->getData();

        $response->assertStatus($expectedCode);
        $this->assertArrayHasKey('error', $data);
    }

    public function testOrderListSuccessCount()
    {
        echo "\n \n \n*** Executing Order List Scenarion (Positive and Negative) *** \n";

        echo "\n > Order Listing Positive Test - Valid Data Count \n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        $response->assertStatus(200);
        $this->assertLessThan(5, count($data));
    }

    public function testOrderListSuccessData()
    {
        echo "\n > Order Listing Positive Test - Valid Data Keys\n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        $response->assertStatus(200);

        foreach ($data as $order) {
            $order = (array) $order;
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('distance', $order);
            $this->assertArrayHasKey('status', $order);
        }
    }

    public function testOrderListFailure()
    {
        echo "\n > Order Listing Negative Test - Invalid Params (page1) - Should get 422\n";
        $query = 'page1=1&limit=4';
        $this->orderListFailure($query, 422);

        echo "\n > Order Listing Negative Test - Invalid Params (limit1) - Should get 422\n";
        $query = 'page=1&limit1=4';
        $this->orderListFailure($query, 422);

        echo "\n > Order Listing Negative Test - Invalid Params Value (page = 0) - Should get 422\n";
        $query = 'page=0&limit=4';
        $this->orderListFailure($query, 422);

        echo "\n > Order Listing Negative Test - Invalid Params Value (limit = 0) - Should get 422\n";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, 422);

        echo "\n > Order Listing Negative Test - Invalid Params Value (limit = -1) - Should get 422\n";
        $query = 'page=1&limit=0';
        $this->orderListFailure($query, 422);
    }

    protected function orderListFailure($query, $expectedCode)
    {
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        $response->assertStatus($expectedCode);
    }
}
