<?php

namespace App\Http\Requests\User;

use App\Helpers\PostmanHelper;
use App\Http\Requests\Core\Interfaces\GetAllRequestInterface;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Models\User;
use App\Postman\PostmanRequestBody;
use App\Postman\PostmanResponse;
use App\Postman\PostmanResponseExample;
use Illuminate\Foundation\Http\FormRequest;

class UserGetAllRequest extends FormRequest implements GetAllRequestInterface, PostmanRequestInterface
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'page' => 'nullable|int',
            'limit' => 'nullable|int'
        ];
    }

    public function getBody(): PostmanRequestBody
    {
        return new PostmanRequestBody();
    }

    public function getResponse(array $request): PostmanResponse
    {
        return new PostmanResponse($request, [
            new PostmanResponseExample(
                PostmanHelper::paginate(User::factory(10)->make()
                    ->each(fn($item) => $item->id = rand(1, 1000))
                )
            )
        ]);
    }
}
