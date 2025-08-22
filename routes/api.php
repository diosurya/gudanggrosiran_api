<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\BlogCategoryController;
use App\Http\Controllers\Api\BlogController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MutationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CategoryProductController;
use App\Http\Controllers\BrandController;
use App\Http\Controllers\TagController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/me', [AuthController::class, 'me']);

	//Logout
	// Route::get('/logout', [AuthController::class, 'logout']);
	Route::post('/auth/logout', [LoginController::class, 'logout']);



	Route::apiResource('blogs', BlogController::class);
	Route::apiResource('categories', BlogCategoryController::class);

	// Route::apiResource('products', ProductController::class);
	Route::apiResource('category_products', CategoryProductController::class);
	// Route::apiResource('tags', TagController::class);
	Route::apiResource('brands', BrandController::class);
});

// Auth
Route::post('/auth/login', [LoginController::class, 'login']);
Route::post('/auth/register', [RegisterController::class, 'register']);

// Customer
Route::get('/customer', [CustomerController::class, 'index']);
Route::get('/customer/{id}', [CustomerController::class, 'show']);
Route::post('/customer', [CustomerController::class, 'store']);
Route::patch('/customer/{id}', [CustomerController::class, 'update']);
Route::delete('/customer/{id}', [CustomerController::class, 'destroy']);
Route::get('/search/{nama}', [CustomerController::class, 'search']);


// Alamat
Route::get('/alamat', [AlamatController::class, 'index']);
Route::get('/alamat/{id}', [AlamatController::class, 'detail']);

// Akun
Route::get('/akun', [AkunController::class, 'index']);
Route::post('/akun', [AkunController::class, 'store']);
Route::delete('/akun/{id}', [AkunController::class, 'destroy']);

// Transaction
Route::post('/transactions/transfer', [TransactionController::class, 'transfer']);

// Mutation
Route::get('/mutations', [MutationController::class, 'index']);



// Additional routes
// Route::get('products/{product}/variants', [ProductController::class, 'variants']);
// Route::post('products/{product}/variants', [ProductController::class, 'storeVariant']);
// Route::get('products/search', [ProductController::class, 'search']);

// Route::get('categories/tree', [BlogCategoryController::class, 'tree']);
