<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AttendanceController;

Route::post('/attendance', [AttendanceController::class, 'store'] );

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
