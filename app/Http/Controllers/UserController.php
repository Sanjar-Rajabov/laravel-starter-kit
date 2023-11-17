<?php

namespace App\Http\Controllers;

use App\Enums\FilterEnum;
use App\Http\Controllers\Core\ResourceController;
use App\Http\Requests\User\UserCreateRequest;
use App\Http\Requests\User\UserUpdateRequest;
use App\Models\User;

class UserController extends ResourceController
{
    protected string $modelClass = User::class;

    protected string $createFormRequest = UserCreateRequest::class;
    protected string $updateFormRequest = UserUpdateRequest::class;

    protected bool $pagination = true;

    protected array $filterable = [
        'id' => FilterEnum::Equal,
        'login' => FilterEnum::Like
    ];

    protected array $searchable = [
        'id' => FilterEnum::Equal,
        'login' => FilterEnum::Like
    ];

    protected function relationsForShow(): array
    {
        return ['addresses'];
    }

}
