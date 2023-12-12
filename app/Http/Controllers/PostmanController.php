<?php

namespace App\Http\Controllers;

use App\Postman\Postman;
use Illuminate\Http\JsonResponse;
use Illuminate\Routing\Controller as BaseController;
use ReflectionException;

class PostmanController extends BaseController
{
    /**
     * @throws ReflectionException
     */
    public function generateCollection(): JsonResponse
    {
        return response()->json(Postman::generate());
    }
}
