<?php

namespace App\Http\Requests\User;

use App\Enums\HttpCode;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\UploadedFile;

class UserUploadRequest extends FormRequest implements PostmanRequestInterface
{
    public function authorize(): true
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'file' => 'required|file',
            'user_id' => 'required|int'
        ];
    }

    // example of form data body
    public function getBody(): PostmanRequestBody
    {
        return new PostmanRequestBody('formdata', [
            'file' => UploadedFile::fake()->image('image.png'),
            'user_id' => 11
        ]);
    }

    public function getResponse(array $request): PostmanResponse
    {
        return new PostmanResponse($request, [
            new PostmanResponseExample(null, HttpCode::NO_CONTENT, null)
        ]);
    }
}
