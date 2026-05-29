<?php

use App\Http\Controllers\Api\BrandController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use Illuminate\Support\Facades\Route;

Route::get('/brands', [BrandController::class, 'index']);
Route::get('/brands/{id}', [BrandController::class, 'show'])->whereNumber('id');

Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show'])->whereNumber('id');

Route::get('/products', [ProductController::class, 'index']);
Route::get('/products/{id}', [ProductController::class, 'show'])->whereNumber('id');
