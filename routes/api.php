<?php

use App\Http\Controllers\MenuController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('menus', MenuController::class);
Route::post('menus/{id}/restore', [MenuController::class, 'restore']);
Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
