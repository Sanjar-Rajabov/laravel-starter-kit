<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Core\Interfaces\GetOneRequestInterface;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Models\User;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use App\Traits\ValidateId;
use Illuminate\Foundation\Http\FormRequest;

class UserGetOneRequest extends FormRequest implements GetOneRequestInterface, PostmanRequestInterface
{
    use ValidateId;

    public function getBody(): PostmanRequestBody
    {
        return new PostmanRequestBody();
    }

    public function getResponse(array $request): PostmanResponse
    {
        return new PostmanResponse($request, [
            new PostmanResponseExample(
                User::factory()->makeOne(['id' => 1])
            )
        ]);
    }
}
