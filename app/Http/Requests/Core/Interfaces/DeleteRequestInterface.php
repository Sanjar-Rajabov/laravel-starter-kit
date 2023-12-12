<?php

namespace App\Http\Requests\Core\Interfaces;

interface DeleteRequestInterface
{
    public function authorize(): bool;

    public function rules(): array;
}
