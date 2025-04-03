<?php

use App\Http\Controllers\AdController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CategoryController;
use App\Http\Middleware\AdminCheck;
use App\Http\Middleware\TokenCheck;
use Illuminate\Support\Facades\Route;

// Авторизация
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/logout', [AuthController::class, 'logout'])
    ->middleware(TokenCheck::class);

// Избранные объявления
Route::get('/ads-likes', [AdController::class, 'getFeatured'])
    ->middleware(TokenCheck::class);
Route::post('/ads/{ad}/like', [AdController::class, 'addToFeaturedAd'])
    ->middleware(TokenCheck::class);
Route::delete('/ads/{ad}/like', [AdController::class, 'removeFromFeaturedAd'])
    ->middleware(TokenCheck::class);

// Объявления
Route::get('/ads', [AdController::class, 'getAcceptedAds']);
Route::get('/ads/admin', [AdController::class, 'getTakenAds'])
    ->middleware(AdminCheck::class);
Route::patch('/ads/{ad}/admin', [AdController::class, 'changeAdStatus'])
    ->middleware(AdminCheck::class);
Route::post('/ads', [AdController::class, 'create'])
    ->middleware(TokenCheck::class);
Route::patch('/ads/{ad}', [AdController::class, 'update'])
    ->middleware(TokenCheck::class);
Route::delete('/ads/{ad}', [AdController::class, 'destroy'])
    ->middleware(TokenCheck::class);
Route::get('/my-ads', [AdController::class, 'getMyAds'])
    ->middleware(TokenCheck::class);

// Категории
Route::get('/categories', [CategoryController::class, 'index']);
Route::post('/categories', [CategoryController::class, 'create'])
    ->middleware(AdminCheck::class);
Route::patch('/categories/{category}', [CategoryController::class, 'update'])
    ->middleware(AdminCheck::class);
Route::delete('/categories/{category}', [CategoryController::class, 'destroy'])
    ->middleware(AdminCheck::class);

