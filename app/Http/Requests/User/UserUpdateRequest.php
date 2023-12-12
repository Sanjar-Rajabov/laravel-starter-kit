<?php

namespace App\Http\Requests\User;

use App\Enums\HttpCode;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Http\Requests\Core\Interfaces\UpdateRequestInterface;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest implements UpdateRequestInterface, PostmanRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => [
                'required',
                'string',
                Rule::unique('users', 'login')->whereNot('id', $this->route('id'))
            ],
            'password' => 'required|string',
            'addresses' => 'required|array',
            'addresses.*.id' => 'nullable|int|exists:addresses,id',
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
            new PostmanResponseExample(null, HttpCode::OK)
        ]);
    }
}
