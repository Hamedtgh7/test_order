<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\LangMiddleware;
use Illuminate\Support\Facades\Route;

Route::middleware('throttle:5,1')->group(function (){
    Route::post('/register',[AuthController::class,'register']);
    Route::post('/login',[AuthController::class,'login']);
});

Route::middleware(['auth:sanctum',LangMiddleware::class])->group(function (){
    Route::get('/orders/total_prices',[OrderController::class,'totalPrices']);
    Route::apiResource('orders',OrderController::class);
    Route::get('/langs/{lang}',[LanguageController::class,'changeLanguage']);
});