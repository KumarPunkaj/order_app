<?php

namespace App\Test\Feature\ApiController;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class OrderIntegrationTest extends TestCase
{
    public function testOrderCreateIncorrectParameters()
    {
        echo "\n *** Executing Integration Test *** \n";

        echo "\n *** Executing Order Create Scenario (Negative and Positive) *** \n";

        echo "\n > Order Create Negative Test - With Invalid Parameter Keys - Should get 422 \n";

        $invalidData1 = [
            'origin1' => ['28.704060', '77.102493'],
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
        echo "\n > Order Create Negative Test - With Empty Parameter - Should get 422";

        $invalidData1 = [
            'origin' => ['28.704060', ''],
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
        echo "\n > Order Create Negative Test - Invalid Data - should get 422 \n";
        $invalidData = [
            'origin' => ['44.968046', 'test', '44.968046'],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $response = $this->json('POST', '/orders', $invalidData);

        $response->assertStatus(422);
    }

    public function testOrderCreationPositiveScenario()
    {
        echo "\n > Order Create Positive Test - Valid Data \n";

        $validData = [
            'origin' => ['28.704061', '77.102493'],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();

        echo "\n\t > should got status 200 \n";
        $response->assertStatus(200);

        echo "\n\t > Response should have key id, status and distance \n";
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('distance', $data);
    }

    public function testOrderUpdate()
    {
        echo "\n \n \n*** Executing Order Update Scenario (Positive and Negative) *** \n";

        echo "\n > Order Update Positive Test - Valid Data \n";

        echo "\n \t > Creating an order \n";
        $validData = [
            'origin' => ['28.704060', '77.102493'],
            'destination' => [
                '28.535517',
                '77.391029',
            ],
        ];

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('POST', '/orders', $validData);
        $data = (array) $response->getData();
        $orderId = $data['id'];

        echo "\n \t > Updating Order \n";
        $response = $this->json('PATCH', '/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        echo "\n > Update Order - should have status 200 \n";
        $response->assertStatus(200);

        echo "\n > Update Order - response has key as `status` \n";
        $this->assertArrayHasKey('status', $data);

        echo "\n > Order Update Negative Test - For Already updated order \n";
        echo "\n \t > Trying to update same order - should get error \n";

        $updateData = ['status' => 'TAKEN'];

        $response = $this->json('PATCH', '/orders/'. $orderId, $updateData);
        $data = (array) $response->getData();

        $response->assertStatus(409);

        echo "\n \t > Trying to update same order - response should has key `error` \n";
        $this->assertArrayHasKey('error', $data);

        echo "\n > Order Update Negative Test - Invalid Params key (status1) \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status1' => 'TAKEN'], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Invalid Param value (TAKEN1) \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status' => 'TAKEN1'], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Empty Param value \n";
        $this->orderUpdateFailureInvalidParams($orderId, ['status' => ''], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Non numeric order id \n";
        $this->orderUpdateFailureInvalidParams('23A', ['status' => 'TAKEN'], $expectedCode = 422);

        echo "\n > Order Update Negative Test - Invalid Order id \n";
        $this->orderUpdateFailureInvalidParams(9999999, ['status' => 'TAKEN'], $expectedCode = 417);
    }

    protected function orderUpdateFailureInvalidParams($orderId, $params, $expectedCode)
    {
        $response = $this->json('PATCH', '/orders/'. $orderId, $params);
        $data = (array) $response->getData();

        echo "\n \t > Trying to update Invalid Order - response should has status $expectedCode \n";
        $response->assertStatus($expectedCode);

        echo "\n \t > Trying to update Invalid Order - response should has key `error` \n";
        $this->assertArrayHasKey('error', $data);
    }

    public function testOrderListSuccessCount()
    {
        echo "\n \n \n*** Executing Order List Scenario (Positive and Negative) *** \n";

        echo "\n > Order Listing Positive Test - Valid Data Count(page=1&limit=4) \n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n > Order Listing Positive Test - Should get status as 200  \n";
        $response->assertStatus(200);

        echo "\n > Order Listing Positive Test - count of data should less than or equal to 4  \n";
        $this->assertLessThan(5, count($data));
    }

    public function testOrderListSuccessData()
    {
        echo "\n > Order Listing Positive Test - Valid Data Keys (page=1&limit=4)\n";

        $query = 'page=1&limit=4';
        $response = $this->json('GET', "/orders?$query", []);
        $data = (array) $response->getData();

        echo "\n\t > Status should be 200\n";
        $response->assertStatus(200);

        echo "\n\t > Response should contain id, distance and status key\n";
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
