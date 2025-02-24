<?php

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\TempImageController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

 // ----------------------------------------------------------------
//  gust route routes
// ----------------------------------------------------------------
Route::get('/front/services', [ServiceController::class, 'getServices']);


Route::post('/authentication', [AuthenticationController::class, 'authenicate'])->name('login');
Route::group(['middleware' => 'auth:sanctum'], function () {
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::post('/service/store', [ServiceController::class, 'store']);
    Route::get('/services', [ServiceController::class, 'index']);
    Route::put('/services/{id}', [ServiceController::class, 'update']);
    Route::get('/service/{id}', [ServiceController::class, 'show']);
    Route::delete('/service/{id}', [ServiceController::class, 'destroy']);
    Route::post('/image/temp', [TempImageController::class, 'store']);
    Route::post('/logout', [AuthenticationController::class, 'logout'])->name('logout');

});
