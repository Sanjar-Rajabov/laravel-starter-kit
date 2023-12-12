<?php

namespace App\Http\Requests\Core\Interfaces;

interface UpdateRequestInterface
{
    public function authorize(): bool;

    public function rules(): array;
}
