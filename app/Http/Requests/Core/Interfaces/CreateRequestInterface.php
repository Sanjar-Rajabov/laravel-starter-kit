<?php

namespace App\Http\Requests\Core\Interfaces;

interface CreateRequestInterface
{
    public function authorize(): bool;

    public function rules(): array;
}
