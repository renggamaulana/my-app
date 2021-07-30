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


// Root Products
Route::resource('products', ProductController::class);



// // Root Users
// Route::get('/users', [UserController::class, 'get']);
// Route::post('/user', [UserController::class, 'post']);
// Route::put('/user/{id}', [UserController::class, 'put']);
// Route::delete('/user/{id}', [UserController::class, 'delete']);
