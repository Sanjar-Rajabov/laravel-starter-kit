<?php

namespace App\Http\Requests\Core\Interfaces;

interface GetAllRequestInterface
{
    public function authorize(): bool;

    public function rules(): array;
}
