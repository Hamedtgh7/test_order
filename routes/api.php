<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\ExchangeRateController;
use App\Http\Controllers\LanguageController;
use App\Http\Controllers\OrderController;
use App\Http\Middleware\LangMiddleware;
use Illuminate\Support\Facades\Route;

Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);

Route::post('/orders/{order}/status',[OrderController::class,'updateStatus']);
Route::get('/total_prices',[OrderController::class,'total_prices']);

Route::middleware(['auth:sanctum',LangMiddleware::class])->group(function (){
    Route::get('/lang/{lang}',[LanguageController::class,'changeLanguage']);
    Route::post('/orders',[OrderController::class,'store']);
    Route::get('/orders',[OrderController::class,'getUserOrders']);
    Route::get('/filters',[OrderController::class,'filterOrders']);
    Route::post('/orders/{order}/uploadReceipt',[OrderController::class,'uploadReceipt']);
});