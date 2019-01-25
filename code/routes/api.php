<?php

use Illuminate\Http\Request;
use App\Http\Middleware\CheckInt;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('orders', 'OrderController@create');
Route::middleware(CheckInt::class)->patch('orders/{id}', 'OrderController@update');
Route::get('orders', 'OrderController@index');
