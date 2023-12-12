<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Core\Interfaces\GetAllRequestInterface;
use Illuminate\Foundation\Http\FormRequest;

class PaginationRequest extends FormRequest implements GetAllRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|int',
            'limit' => 'nullable|int'
        ];
    }
}
