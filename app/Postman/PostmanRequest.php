<?php

namespace App\Postman;

use App\Http\Requests\Core\Interfaces\HasBodyExampleInterface;
use App\Http\Requests\Core\Interfaces\HasParamsExampleInterface;
use Illuminate\Routing\Route;
use ReflectionClass;
use ReflectionException;

class PostmanRequest
{
    public function __construct(
        protected Route           $route,
        protected ReflectionClass $controllerClass,
        protected string          $methodName,
        protected object|null     $requestClass = null
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function toArray(): array
    {
        if (!empty($this->requestClass)) {
            $body = $this->requestClass instanceof HasBodyExampleInterface ? $this->requestClass->getBody()->toArray() : [];
            $params = $this->requestClass instanceof HasParamsExampleInterface ? $this->requestClass->getParams()->toArray() : [];
        } else {
            $body = [];
            $params = [];
        }

        return [
            'method' => ucfirst(self::getActualRouteMethod($this->route)),
            'header' => [],
            'url' => (new PostmanUrl($this->route->uri, $params))->toArray(),
            'description' => (new PostmanDescription($this->controllerClass, $this->methodName, $this->requestClass))->toArray(),
            'body' => $body
        ];
    }


    protected static function getActualRouteMethod(Route $route)
    {
        foreach (PostmanRoute::$allowedMethods as $allowedMethod) {
            if (in_array($allowedMethod, $route->methods)) {
                return $allowedMethod;
            }
        }
    }
}
