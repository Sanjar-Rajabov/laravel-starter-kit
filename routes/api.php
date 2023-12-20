<?php

use App\Helpers\RouteHelper;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

RouteHelper::resource('user', UserController::class);
RouteHelper::resource('usere', UserController::class);
Route::prefix('user')->group(function () {
    Route::get('example-request', [UserController::class, 'getAll']);
});
