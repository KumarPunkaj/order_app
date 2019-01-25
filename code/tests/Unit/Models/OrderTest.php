<?php

use Illuminate\Foundation\Testing\WithoutMiddleware;

class OrderTest extends Tests\TestCase
{
    use WithoutMiddleware;

    function testTakeOrder()
    {
        echo "\n *** Unit Test - Models::Order - Method:takeOrder *** \n";

        $order = new \App\Http\Models\Order();

        $this->assertContains($order->takeOrder(10000), [true, false]);
    }
}
