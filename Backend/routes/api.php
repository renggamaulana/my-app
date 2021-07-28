<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CategoryController;
use App\Models\Product;

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

// Root Products
// Route::get('/products', [ProductController::class, 'get']);

Route::get('/products', function () {
    $product = Product::get();
    return response()->json(
        $product
    );
});
Route::get('/product/{id}', [ProductController::class, 'getById']);
Route::post('/product', [ProductController::class, 'post']);
Route::put('/product/{id}', [ProductController::class, 'put']);
Route::delete('/product/{id}', [ProductController::class, 'delete']);


// // Root Users
// Route::get('/users', [UserController::class, 'get']);
// Route::post('/user', [UserController::class, 'post']);
// Route::put('/user/{id}', [UserController::class, 'put']);
// Route::delete('/user/{id}', [UserController::class, 'delete']);

// Root Categories
Route::get('/categories', [CategoryController::class, 'get']);
Route::get('/category/{id}', [CategoryController::class, 'getById']);
Route::post('/category', [CategoryController::class, 'post']);
Route::put('/category/{id}', [CategoryController::class, 'put']);
Route::delete('/category/{id}', [CategoryController::class, 'delete']);

// Root Carts
Route::get('/carts', [CategoryController::class, 'get']);
Route::post('/carts', [CategoryController::class, 'post']);
Route::delete('/carts/{id}', [CategoryController::class, 'delete']);
