<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Contracts\CreateFormRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

class UserCreateRequest extends FormRequest implements CreateFormRequestInterface
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
}
