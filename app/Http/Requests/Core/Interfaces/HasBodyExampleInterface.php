<?php

namespace App\Http\Requests\Core\Interfaces;

use App\Postman\PostmanRequestBody;

interface HasBodyExampleInterface
{
    public function getBody(): PostmanRequestBody;
}
