<?php

namespace App\Http\Requests\Core\Interfaces;

use App\Postman\PostmanParams;

interface HasParamsExampleInterface
{
    public function getParams(): PostmanParams;
}
