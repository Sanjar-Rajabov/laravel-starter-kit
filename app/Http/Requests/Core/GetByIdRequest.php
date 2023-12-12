<?php

namespace App\Http\Requests\Core;

use App\Http\Requests\Core\Interfaces\GetOneRequestInterface;
use App\Traits\ValidateId;
use Illuminate\Foundation\Http\FormRequest;

class GetByIdRequest extends FormRequest implements GetOneRequestInterface
{
    use ValidateId;
}
