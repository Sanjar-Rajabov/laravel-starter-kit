<?php

namespace App\Postman;

use App\Http\Requests\Core\Interfaces\CreateRequestInterface;
use App\Http\Requests\Core\Interfaces\DeleteRequestInterface;
use App\Http\Requests\Core\Interfaces\GetAllRequestInterface;
use App\Http\Requests\Core\Interfaces\GetOneRequestInterface;
use App\Http\Requests\Core\Interfaces\PostmanRequestInterface;
use App\Http\Requests\Core\Interfaces\UpdateRequestInterface;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionClass;
use ReflectionException;

class PostmanRoute
{
    public static array $dataTypes = ['string', 'int', 'integer', 'float', 'array', 'numeric', 'image', 'file', 'date', 'bool', 'boolean'];
    public static array $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

    /**
     * @throws ReflectionException
     */
    public static function generate(): array
    {
        /** @var Router $router */
        $router = app(Router::class);

        $routes = collect($router->getRoutes()->getRoutes());
        $groups = $routes->where(fn($item) => in_array('api', $item->action['middleware']))->groupBy('action.prefix');

        $folders = [];
        foreach ($groups->keys() as $key) {
            $explode = explode('/', trim($key, '/'));
            $count = count($explode);

            if ($count > 1 || $explode[0] != "") {
                $iterationFolders = [];
                $folderKey = null;
                for ($i = 0; $i <= $count - 1; $i++) {
                    if ($i === $count - 1) { // if last item
                        $items = [];
                        foreach ($groups[$key] as $route) {
                            $items[] = self::generateRoute($route);
                        }

                        $array = [
                            'name' => ucfirst($explode[$i]),
                            'item' => $items
                        ];
                    } else {
                        $array = [
                            'name' => ucfirst($explode[$i]),
                            'item' => []
                        ];
                    }

                    if ($i === 0) {
                        for ($f = 0; $f < count($folders); $f++) {
                            if (!empty($folders[$f]['name']) && $folders[$f]['name'] == $array['name']) {
                                $folderKey = $f;
                                $iterationFolders = $folders[$f];
                            }
                        }
                        if ($folderKey === null) {
                            $iterationFolders[$i] = $array;
                        }
                    } else {
                        if ($folderKey === null) {
                            $iterationFolders[$i - 1]['item'][] = $array;
                        } else {
                            $iterationFolders['item'][] = $array;
                        }
                    }
                }
                if ($folderKey === null) {
                    $folders = array_merge($folders, $iterationFolders);
                } else {
                    $folders[$folderKey] = $iterationFolders;
                }
            } else {
                foreach ($groups[$key] as $route) {
                    $folders[] = self::generateRoute($route);
                }
            }
        }

        return $folders;
    }

    /**
     * @throws ReflectionException
     */
    protected static function generateRoute(Route $route, bool $addControllerToName = false): array
    {
        $action = explode('@', $route->action['uses']);
        $className = $action[0];
        $methodName = $action[1];
        $reflectionClass = new ReflectionClass($className);

        $requestClass = self::getRequestClass($reflectionClass, $methodName);

        $request = (new PostmanRequest($route, $reflectionClass, $methodName, $requestClass))->toArray();

        return [
            'name' => self::getName($addControllerToName, self::camelCaseToWords($action[1]), $reflectionClass),
            'request' => $request,
            'response' => $requestClass instanceof PostmanRequestInterface ? $requestClass->getResponse($request)->toArray() : []
        ];
    }

    public static function getName(bool $addControllerToName, string $methodName, ReflectionClass $reflectionClass): string
    {
        if (!$addControllerToName) {
            return ucfirst($methodName);
        } else {
            return self::camelCaseToWords(
                    str_replace('Controller', '', $reflectionClass->getShortName())
                ) . ' ' . strtolower($methodName);
        }
    }


    protected static function camelCaseToWords(string $string): string
    {
        return trim(
            implode(
                ' ',
                preg_split('/(?=[A-Z])/', $string)
            )
        );
    }


    /**
     * @return mixed|null
     * @throws ReflectionException
     */
    protected static function getRequestClass(ReflectionClass $controllerClass, string $methodName): mixed
    {
        foreach ($controllerClass->getMethod($methodName)->getParameters() as $parameter) {
            $name = $parameter->getType()?->getName();

            if (interface_exists($name)) {
                $requestClassName = match ($name) {
                    GetAllRequestInterface::class => $controllerClass->getProperty('getAllRequest')->getDefaultValue(),
                    GetOneRequestInterface::class => $controllerClass->getProperty('getOneRequest')->getDefaultValue(),
                    CreateRequestInterface::class => $controllerClass->getProperty('createRequest')->getDefaultValue(),
                    UpdateRequestInterface::class => $controllerClass->getProperty('updateRequest')->getDefaultValue(),
                    DeleteRequestInterface::class => $controllerClass->getProperty('deleteRequest')->getDefaultValue(),
                };
                break;
            }
            if (class_exists($name)) {
                $requestClassName = $name;
            }
        }

        return !empty($requestClassName) ? new $requestClassName : null;
    }

}
