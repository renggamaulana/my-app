<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;

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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });


// Route::get('/products', 'ProductConroller@get');
// Route::post('/product', 'ProductConroller@post');
// Route::put('/product/{id}', 'ProductConroller@put');
// Route::delete('/product/{id}', 'ProductConroller@delete');

// Route::resource('products', ProductController::class); 


Route::get('/products', [ProductController::class, 'get']);
Route::get('/product/{id}', [ProductController::class, 'getById']);
Route::post('/product', [ProductController::class, 'post']);
Route::put('/product/{id}', [ProductController::class, 'put']);
Route::delete('/product/{id}', [ProductController::class, 'delete']);
