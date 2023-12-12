<?php

namespace App\Http\Requests\User;

use App\Enums\HttpCode;
use App\Http\Requests\Core\Interfaces\CreateRequestInterface;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest implements CreateRequestInterface, PostmanRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|unique:users,login',
            'password' => 'required|string',
            'addresses' => 'required|array',
            'addresses.*.region' => 'required|string',
            'addresses.*.district' => 'required|string',
            'addresses.*.address' => 'required|string'
        ];
    }

    public function getBody(): PostmanRequestBody
    {
        return new PostmanRequestBody('raw', [
            'login' => 'test login',
            'password' => 'test123',
            'addresses' => [
                [
                    'region' => 'test region',
                    'district' => 'test district',
                    'address' => 'test address'
                ]
            ]
        ]);
    }

    public function getResponse(array $request): PostmanResponse
    {
        return new PostmanResponse($request, [
                new PostmanResponseExample(
                    null,
                    HttpCode::CREATED
                )
            ]
        );
    }
}
