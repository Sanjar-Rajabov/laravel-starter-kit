<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Contracts\PaginationFormRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest implements PaginationFormRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'required|int',
            'page-limit' => 'required|int'
        ];
    }
}
