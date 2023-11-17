<?php

namespace App\APIServices\Core;

use Exception;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

abstract class APIService
{
    private string $baseUrl;

    protected function setBaseUrl(string $baseUrl): void
    {
        $this->baseUrl = $baseUrl;
    }

    protected function http(): PendingRequest
    {
        return Http::baseUrl($this->baseUrl)->acceptJson();
    }

    /**
     * @throws Exception
     */
    protected function post(string $url, $body = [], array $headers = []): Response
    {
        $http = $this->http();

        if (!empty($headers)) {
            $http->replaceHeaders($headers);
        }
        $response = $http->post($url, $body);

        $this->checkResponse($response, $body);

        return $response;
    }

    /**
     * @throws Exception
     */
    protected function get(string $url, $query = [], array $headers = []): Response
    {
        $http = $this->http();

        if (!empty($headers)) {
            $http->replaceHeaders($headers);
        }

        $response = $http->get($url, $query);

        $this->checkResponse($response, $query);

        return $response;
    }

    /**
     * @throws Exception
     */
    protected function checkResponse($response, $body): void
    {
        if ($response->failed()) {
            $this->throw($response, $body);
        }
    }

    /**
     * @throws Exception
     */
    protected function throw($response, $body)
    {
        $explode = explode('\\', get_called_class());
        $service = $explode[count($explode) - 1];

        $key = debug_backtrace()[1]['class'] == get_parent_class($this) ? 2 : 1;
        $method = debug_backtrace()[$key]['function'];

        throw new Exception("$service $method: body - " . json_encode($body) . ", response - {$response->body()}, response status - {$response->status()}");
    }
}
