<?php

namespace App\Postman;

class PostmanParams
{
    public function __construct(
        protected array $parameters
    )
    {
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->parameters as $key => $value) {
            $result[] = [
                'key' => $key,
                'value' => $value,
                'disabled' => true
            ];
        }

        return $result;
    }
}
