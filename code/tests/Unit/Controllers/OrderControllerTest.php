<?php

use App\Http\Controllers\OrderController;
use App\Http\Models\Order;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Http\JsonResponse;
// use Mockery;

class OrderControllerTest extends Tests\TestCase
{
    use WithoutMiddleware;

    protected static $allowedOrderStatus = [
        Order::UNASSIGNED_ORDER_STATUS,
        Order::ASSIGNED_ORDER_STATUS,
    ];

    public function setUp()
    {
        parent::setUp();

        $this->faker = Faker\Factory::create();
        $this->orderServiceMock = \Mockery::mock(\App\Http\Services\Order::class);
        $this->responseMock = $this->createResponseMock();

         $this->app->instance(OrderController::class,
            new OrderController(
                $this->orderServiceMock,
                $this->responseMock
            )
        );
    }

    public function tearDown()
    {
        \Mockery::close();
    }

    public function testCreateWithValidData()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:create - with valid data *** \n";

        $order = $this->generateRandomOrder();

        $params = [
            'origin' => [$this->faker->latitude(), $this->faker->longitude()],
            'destination' => [$this->faker->latitude(), $this->faker->longitude()]
        ];

        //Order Service will return success
        $this->orderServiceMock
            ->shouldReceive('createOrder')
            ->once()
            ->andReturn($order);

        $response = $this->call('POST', '/orders', $params);
        $data = (array) $response->getData();

        $response->assertStatus(200);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('distance', $data);
    }

    public function testCreateWithInvalidData()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:create - with in-valid data *** \n";

        $order = $this->generateRandomOrder();

        $params = [
            'origin' => [$this->faker->latitude(), $this->faker->longitude()],
            'destination' => [$this->faker->latitude(), $this->faker->longitude()]
        ];

        //Order Service will return failure
        $this->orderServiceMock
            ->shouldReceive('createOrder')
            ->once()
            ->andReturn(false);

        $this->orderServiceMock->error = 'INVALID_PARAMETERS';
        $this->orderServiceMock->errorCode = JsonResponse::HTTP_UNPROCESSABLE_ENTITY;

        $response = $this->call('POST', '/orders', $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
    }

    public function testUpdateValid()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:update - with valid data *** \n";

        $id = $this->faker->randomDigit();

        $order = $this->generateRandomOrder($id);

        //Valid order id provided
        $this->orderServiceMock
            ->shouldReceive('getOrderById')
            ->once()
            ->with($id)
            ->andReturn($order);

        $this->orderServiceMock
            ->shouldReceive('takeOrder')
            ->once()
            ->with($id)
            ->andReturn(true);

        $params = ['status' => 'TAKEN'];

        $response = $this->call('PATCH', "/orders/{$id}", $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_OK);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertEquals('SUCCESS', $data['status']);
    }

    public function testUpdateInValid()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:update - with in - valid data *** \n";

        $id = $this->faker->randomDigit();

        $order = $this->generateRandomOrder($id);

        //In Valid order id provided
        $this->orderServiceMock
            ->shouldReceive('getOrderById')
            ->once()
            ->with($id)
            ->andThrow(\Illuminate\Database\Eloquent\ModelNotFoundException::class);

        $params = ['status' => 'TAKEN'];

        $response = $this->call('PATCH', "/orders/{$id}", $params);
        $data = (array) $response->getData();

        $response->assertStatus(JsonResponse::HTTP_EXPECTATION_FAILED);

        $this->assertInternalType('array', $data);
        $this->assertArrayHasKey('error', $data);
        $this->assertEquals('INVALID_ID', $data['error']);
    }

    public function testIndexWithValidParams()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:index - with valid data *** \n";

        $page = 1;
        $limit = 5;

        $orderList = [];

        for ($i=0; $i < 5; $i++) {
            $orderList[] = $this->generateRandomOrder();
        }

        $orderRecordCollection = new \Illuminate\Database\Eloquent\Collection($orderList);

        //In Valid order id provided
        $this->orderServiceMock
            ->shouldReceive('getList')
            ->once()
            ->with($page, $limit)
            ->andReturn($orderRecordCollection);

        $params = ['page' => $page, 'limit' => $limit];

        $response = $this->call('GET', "/orders", $params);
        $data = $response->getData();

        $response->assertStatus(JsonResponse::HTTP_OK);

        $this->assertInternalType('array', $data);

        $this->assertArrayHasKey('id', (array) $data[0]);
        $this->assertArrayHasKey('distance', (array) $data[0]);
        $this->assertArrayHasKey('status', (array) $data[0]);
    }

    public function testIndexWithBlankData()
    {
        echo "\n *** Unit Test - Controller::OrderController - Method:index - with No content *** \n";

        $page = 1;
        $limit = 5;

        $orderRecordCollection = new \Illuminate\Database\Eloquent\Collection([]);

        //In Valid order id provided
        $this->orderServiceMock
            ->shouldReceive('getList')
            ->once()
            ->andReturn($orderRecordCollection);

        $params = ['page' => $page, 'limit' => $limit];

        $response = $this->call('GET', "/orders", $params);

        $response->assertStatus(JsonResponse::HTTP_NO_CONTENT);
    }

    /**
     * @param int|null $id
     *
     * @return Order
     */
    private function generateRandomOrder($id = null)
    {
        $id = $id?:$this->faker->randomDigit();

        $order = new Order();
        $order->id = $id;
        $order->status = $this->faker->randomElement(self::$allowedOrderStatus);
        $order->distance_id = $this->faker->randomDigit();
        $order->distance_value = $this->faker->numberBetween(1000, 9999);
        $order->created_at = $this->faker->dateTimeBetween();
        $order->updated_at = $this->faker->dateTimeBetween();

        return $order;
    }

    /**
     * @return Mockery_2_App_Http_Response_Response
     */
    private function createResponseMock()
    {
        $messageHelperMock = \Mockery::mock('\App\Helpers\MessageHelper')->makePartial();

        $responseMock = \Mockery::mock('\App\Http\Response\Response[formatOrderAsResponse]', [$messageHelperMock]);

        $responseMock
            ->shouldReceive('formatOrderAsResponse')
             ->andReturnUsing(function($argument) {
                return [
                    'id' => $argument->id,
                    'status' => $argument->status,
                    'distance' => $argument->distance_value,
                ];
            });

        return $responseMock;
    }
}
