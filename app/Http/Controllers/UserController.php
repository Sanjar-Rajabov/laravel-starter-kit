<?php

namespace App\Http\Controllers;

use App\Enums\FilterTypes;
use App\Http\Controllers\Core\ResourceController;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserGetAllRequest;
use App\Http\Requests\User\UserGetOneRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;

class UserController extends ResourceController
{
    protected string $modelClass = User::class;

    protected string $getAllRequest = UserGetAllRequest::class;
    protected string $getOneRequest = UserGetOneRequest::class;
    protected string $createRequest = UserCreateRequest::class;
    protected string $updateRequest = UserUpdateRequest::class;

    protected bool $pagination = true;

    protected array $filterable = [
        'id' => FilterTypes::Equal,
        'login' => FilterTypes::Like
    ];

    protected array $searchable = [
        'id' => FilterTypes::Equal,
        'login' => FilterTypes::Like
    ];

}
