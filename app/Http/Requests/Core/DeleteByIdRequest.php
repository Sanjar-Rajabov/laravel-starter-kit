<?php

namespace App\Http\Requests\Core;

use App\Enums\HttpCode;
use App\Http\Requests\Core\Interfaces\DeleteRequestInterface;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use App\Traits\ValidateId;
use Illuminate\Foundation\Http\FormRequest;

class DeleteByIdRequest extends FormRequest implements DeleteRequestInterface, PostmanRequestInterface
{
    use ValidateId;

    public function getBody(): PostmanRequestBody
    {
        return new PostmanRequestBody();
    }

    public function getResponse(array $request): PostmanResponse
    {
        return new PostmanResponse($request, [
            new PostmanResponseExample(null, HttpCode::NO_CONTENT, null)
        ]);
    }
}
