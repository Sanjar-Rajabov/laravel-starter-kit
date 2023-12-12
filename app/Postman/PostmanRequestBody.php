<?php

namespace App\Postman;

use Illuminate\Http\Testing\File;

class PostmanRequestBody
{
    public function __construct(
        protected string $mode = '',
        protected mixed  $body = null
    )
    {
    }

    public function toArray(): array
    {
        if (empty($this->mode) || empty($this->body)) {
            return [];
        }

        return [
            'mode' => $this->mode,
            $this->mode => $this->getBody(),
            'options' => $this->getOptions()
        ];
    }

    private function getBody()
    {
        if ($this->mode == 'raw') {
            return is_string($this->body) ? $this->body : json_encode($this->body, JSON_PRETTY_PRINT);
        }

        $formData = [];
        foreach ($this->body as $key => $value) {
            if ($value instanceof File) {
                $formData[] = [
                    'key' => $key,
                    'type' => 'file',
                    'src' => $value->getPath()
                ];
            } else {
                $formData[] = [
                    'key' => $key,
                    'type' => 'text',
                    'value' => "$value"
                ];
            }
        }

        return $formData;
    }

    private function getOptions(): array
    {
        if ($this->mode == 'formdata') {
            return [];
        }

        return [
            'raw' => [
                'language' => 'json'
            ]
        ];
    }
}
