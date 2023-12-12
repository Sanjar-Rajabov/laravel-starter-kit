<?php

namespace App\Http\Requests\Core\Interfaces;

use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;

interface PostmanRequestInterface
{
    public function getBody(): PostmanRequestBody;

    public function getResponse(array $request): PostmanResponse;
}
