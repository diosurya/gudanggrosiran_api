<?php

use App\Http\Controllers\AkunController;
use App\Http\Controllers\AlamatController;
use App\Http\Controllers\Api\Auth\LoginController;
use App\Http\Controllers\Api\Auth\RegisterController;
use App\Http\Controllers\Api\Admin\BlogCategoryController;
use App\Http\Controllers\Api\Admin\BlogController;
use App\Http\Controllers\Api\Admin\MediaController;
use App\Http\Controllers\Api\Admin\CategoryProductController;
use App\Http\Controllers\Api\Admin\BrandController;
use App\Http\Controllers\Api\Admin\TagController;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\Landing\StoreController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\Api\Landing\ProductController as ProductLanding;
use App\Http\Controllers\Api\Landing\BlogController as BlogLanding;
use App\Http\Controllers\Api\Landing\PageController as PageLanding;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
	Route::get('/me', [AuthController::class, 'me']);

	//Logout
	Route::post('/admin/auth/logout', [LoginController::class, 'logout']);

	Route::apiResource('admin/blogs', BlogController::class);
	Route::apiResource('admin/categories', BlogCategoryController::class);

	Route::apiResource('admin/category_products', CategoryProductController::class);
	Route::apiResource('admin/tags', TagController::class);
	Route::apiResource('admin/brands', BrandController::class);

	Route::prefix('admin/products')->group(function () {
		Route::get('/', [\App\Http\Controllers\Api\Admin\ProductController::class, 'index']);
		Route::post('/', [\App\Http\Controllers\Api\Admin\ProductController::class, 'store']);
		Route::get('/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'show']);
		Route::put('/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'update']);
		Route::delete('/{id}', [\App\Http\Controllers\Api\Admin\ProductController::class, 'destroy']);
		
		// Bulk operations
		Route::patch('admin/bulk-status', [\App\Http\Controllers\Api\Admin\ProductController::class, 'bulkUpdateStatus']);
		Route::delete('admin/bulk-delete', [\App\Http\Controllers\Api\Admin\ProductController::class, 'bulkDelete']);
	});


	// Media
	Route::prefix('admin/media')->group(function () {
        Route::get('/', [MediaController::class, 'index']);
        Route::post('/upload', [MediaController::class, 'upload']);
        Route::get('/{id}', [MediaController::class, 'show']);
        Route::put('/{id}', [MediaController::class, 'update']);
        Route::delete('/{id}', [MediaController::class, 'destroy']);
        Route::get('/type/{type}', [MediaController::class, 'getByType']);
        Route::get('/search/query', [MediaController::class, 'search']);
        Route::get('/stats/overview', [MediaController::class, 'getStats']);
    });

	Route::apiResource('admin/pages', \App\Http\Controllers\Api\Admin\PageController::class);
});



// Auth
Route::post('/admin/auth/login', [LoginController::class, 'login']);
Route::post('/admin/auth/register', [RegisterController::class, 'register']);

// Stores
Route::get('/stores', [StoreController::class, 'index']);
Route::get('/stores/{id}', [StoreController::class, 'show']);

// Blogs
Route::prefix('landing/blogs')->group(function () {
    Route::get('/', [BlogLanding::class, 'index']);
    Route::get('/{slug}', [BlogLanding::class, 'show']);
    Route::get('/published/{slug}', [BlogLanding::class, 'publishedShow']);
});

// Products
Route::prefix('products')->group(function () {
    Route::get('/landing', [ProductLanding::class, 'publishedIndex']);
    Route::get('/landing/{slug}', [ProductLanding::class, 'publishedShow']);
});

// Pages
Route::prefix('pages')->group(function () {
    Route::get('/', [\App\Http\Controllers\Api\Landing\PageController::class, 'index']);
    Route::get('/slug/{slug}', [\App\Http\Controllers\Api\Landing\PageController::class, 'showBySlug']);
});