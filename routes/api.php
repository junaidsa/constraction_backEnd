<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
Route::post('/authentication', [AuthenticationController::class, 'authenicate'])->name('login');
Route::group(['middleware' => 'auth:sanctum'  ], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/service/store', [ServiceController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');
});
