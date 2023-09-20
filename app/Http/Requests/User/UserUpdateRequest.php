<?php

namespace App\Http\Requests\User;

use App\Http\Requests\Contracts\UpdateFormRequestInterface;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest implements UpdateFormRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'login' => 'required|string|unique:users,login,' . User::query()->findOrFail($this->route('id'))->id,
            'password' => 'required|string',
            'addresses' => 'required|array',
            'addresses.*.id' => 'nullable|int|exists:addresses,id',
            'addresses.*.region' => 'required|string',
            'addresses.*.district' => 'required|string',
            'addresses.*.address' => 'required|string'
        ];
    }
}
