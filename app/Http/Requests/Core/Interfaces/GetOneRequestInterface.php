<?php

namespace App\Http\Requests\Core\Interfaces;

interface GetOneRequestInterface
{
    public function authorize(): bool;

    public function rules(): array;
}
