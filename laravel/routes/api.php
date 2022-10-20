<?php

use Illuminate\Support\Facades\Route;
use \App\Http\Controllers\Api\v1\ArticlesController;
use \App\Http\Controllers\Api\v1\ProductsController;
// v1 Api
Route::prefix('v1')->group(function () {
    // articles
    Route::group(['prefix' => 'articles'], function () {
        Route::get('/', [ArticlesController::class, 'index'])->name('articles');
        Route::post('/import', [ArticlesController::class, 'import'])->name('articles.import');
    });
    // products
    Route::group(['prefix' => 'products'], function () {
        Route::get('/', [ProductsController::class, 'index'])->name('products');
        Route::post('/import', [ProductsController::class, 'import'])->name('products.import');
    });
});
