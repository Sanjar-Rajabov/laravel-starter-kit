<?php

namespace App\Http\Requests\Core\Interfaces;

use App\Postman\PostmanResponse;

interface HasResponseExampleInterface
{
    public function getResponse(array $request): PostmanResponse;
}
