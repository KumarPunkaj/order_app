<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckInt;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Registe API routes here.
| Defined routes for orderController to manage orders
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('orders', 'OrderController@create');
Route::middleware(CheckInt::class)->patch('orders/{id}', 'OrderController@update');
Route::get('orders', 'OrderController@index');
