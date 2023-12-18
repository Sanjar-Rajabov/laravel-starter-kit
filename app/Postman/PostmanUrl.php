<?php

namespace App\Postman;

class PostmanUrl
{
    public function __construct(
        protected string $uri,
        protected array  $params = []
    )
    {
    }

    public function toArray(): array
    {
        $uri = $this->uri;
        $paths = [];
        $variables = [];

        foreach (explode('/', trim($uri, '\\/')) as $path) {
            if (empty($path)) {
                continue;
            }
            if (str_contains($path, '{')) {
                $key = str_replace(['{', '}'], '', $path);
                $newPath = ':' . $key;
                $uri = str_replace($path, $newPath, $uri);
                $variables[] = [
                    'key' => $key,
                    'value' => 1
                ];
                $path = $newPath;
            }

            $paths[] = $path;
        }

        return [
            'raw' => Postman::$baseUrlVariable . '/' . $uri,
            'host' => [Postman::$baseUrlVariable],
            'path' => $paths,
            'query' => $this->params,
            'variable' => $variables
        ];
    }
}
