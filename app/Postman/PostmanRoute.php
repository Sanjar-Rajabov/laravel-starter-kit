<?php

namespace App\Postman;

use App\Http\Requests\Core\Interfaces\CreateRequestInterface;
use App\Http\Requests\Core\Interfaces\DeleteRequestInterface;
use App\Http\Requests\Core\Interfaces\GetAllRequestInterface;
use App\Http\Requests\Core\Interfaces\GetOneRequestInterface;
use App\Http\Requests\Core\Interfaces\HasResponseExampleInterface;
use App\Http\Requests\Core\Interfaces\UpdateRequestInterface;
use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionClass;
use ReflectionException;

class PostmanRoute
{
    public static array $dataTypes = ['string', 'int', 'integer', 'float', 'array', 'numeric', 'image', 'file', 'date', 'bool', 'boolean'];
    public static array $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

    protected static array $folders = [];

    /**
     * @throws ReflectionException
     */
    public static function generate(): array
    {
        /** @var Router $router */
        $router = app(Router::class);

        $routes = collect($router->getRoutes()->getRoutes());
        $groups = $routes->where(fn($item) => in_array('api', $item->action['middleware']))->groupBy('action.prefix');

        $temp = self::$folders = [];

        foreach ($groups as $key => $group)
            self::generateItems($temp, $key);

        foreach ($temp as $folder)
            self::generateItemsFix(self::$folders, $folder);

        $folders = [];

        foreach (self::$folders as $folder) {
            self::setRoute($folder, $groups);
            $folders[] = $folder;
        }

        if (in_array('', $groups->keys()->toArray())) {
            foreach ($groups[''] as $route) {
                $folders[] = self::generateRoute($route);
            }
        }

        return $folders;
    }


    /**
     * @throws ReflectionException
     */
    protected static function setRoute(array &$folder, $groups, string $prefix = null): void
    {
        if (empty($prefix)) {
            $prefix = lcfirst($folder['name']);
        }

        if (!empty($folder['item']) && empty($folder['request'])) {
            $items = [];
            foreach ($folder['item'] as $item) {
                $itemPrefix = $prefix . '/' . lcfirst($item['name']);
                if (!empty($groups[$itemPrefix])) {
                    foreach ($groups[$itemPrefix] as $route) {
                        $item['item'][] = self::generateRoute($route);
                    }
                }

                $items[] = $item;
            }

            $folder['item'] = $items;
        } elseif ($folder['item'] === []) {
            foreach ($groups[$prefix] as $route) {
                $folder['item'][] = self::generateRoute($route);
            }
        }

        if (!empty($groups[$prefix])) {
            foreach ($groups[$prefix] as $route) {
                $folder['item'][] = self::generateRoute($route);
            }
        }
    }

    private static function generateItemsFix(array &$folders, array $folder): void
    {
        $newFolder = $folder;
        $newItems = [];

        foreach ($newFolder['item'] as $fol)
            self::generateItemsFix($newItems, $fol);

        $newFolder['item'] = $newItems;
        $folders[] = $newFolder;
    }

    protected static function generateItems(array &$array, string $item): void
    {
        $levels = explode('/', $item);

        if ($levels[0] === "") {
            return;
        }

        $newArray = [
            'name' => ucfirst($levels[0]),
            'item' => []
        ];

        if (count($levels) > 1) {
            $newString = str_replace($levels[0] . '/', '', $item);
            self::generateItems($newArray['item'], $newString);
        }

        if (array_key_exists($newArray['name'], $array)) {

            $array[$newArray['name']]['item'] = [
                ...$array[$newArray['name']]['item'],
                ...$newArray['item']
            ];

        } else $array[$newArray['name']] = $newArray;
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
            'response' => $requestClass instanceof HasResponseExampleInterface ? $requestClass->getResponse($request)->toArray() : []
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
