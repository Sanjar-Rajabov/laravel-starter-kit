<?php

use App\Helpers\RouteHelper;
use App\Http\Controllers\PostmanController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

RouteHelper::resource('user', UserController::class);

Route::get('postman/generate-collection', [PostmanController::class, 'generateCollection']);
