<?php

use App\Http\Controllers\Api\V1\ArticleController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

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

// articles
Route::group(['prefix' => 'articles'], function () {
    Route::get('/', [ArticleController::class, 'index'])->name('articles');
    Route::post('/store-json', [ArticleController::class, 'storeJson'])->name('articles.store.json');
});

// products
Route::group(['prefix' => 'products'], function () {
    Route::get('/', [ProductController::class, 'index'])->name('products');
    Route::post('/store-json', [ProductController::class, 'storeJson'])->name('products.store.json');
});
