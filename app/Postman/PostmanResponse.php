<?php

namespace App\Postman;

class PostmanResponse
{
    /**
     * @param PostmanResponseExample[] $examples
     */
    public function __construct(
        protected array $request = [],
        protected array $examples = []
    )
    {
    }

    public function toArray(): array
    {
        $result = [];

        foreach ($this->examples as $example) {
            $result[] = $example->toArray($this->request);
        }

        return $result;
    }
}
