<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Contracts\UpdateFormRequestInterface;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UserUpdateRequest extends FormRequest implements UpdateFormRequestInterface
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
                Rule::unique('users', 'login')->ignore($this->route('id'))
            ],
            'password' => 'required|string',
            'addresses' => 'required|array',
            'addresses.*.id' => 'nullable|int|exists:addresses,id',
            'addresses.*.region' => 'required|string',
            'addresses.*.district' => 'required|string',
            'addresses.*.address' => 'required|string'
        ];
    }
}
