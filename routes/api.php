<?php

use App\Http\Controllers\PostmanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::prefix('user')->controller(UserController::class)->group(function () {
    Route::get('', 'index');
    Route::get('{id}', 'show');
    Route::post('', 'create');
    Route::put('{id}', 'update');
    Route::delete('{id}', 'delete');
});

Route::get('postman/generate-collection', [PostmanController::class, 'generateCollection']);
