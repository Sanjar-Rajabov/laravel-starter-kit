<?php

namespace App\Helpers\Postman;

use Illuminate\Routing\Route;
use Illuminate\Routing\Router;
use ReflectionClass;
use ReflectionException;

class PostmanRoute
{

    protected static array $dataTypes = ['string', 'int', 'integer', 'float', 'array', 'numeric', 'image', 'file', 'date'];
    protected static array $allowedMethods = ['GET', 'POST', 'PATCH', 'PUT', 'DELETE'];

    /**
     * @throws ReflectionException
     */
    public static function generate(): array
    {
        /** @var Router $router */
        $router = app(Router::class);

        $routes = collect($router->getRoutes()->getRoutes());
        $groups = $routes->where(fn($item) => in_array('api', $item->action['middleware']))->groupBy('action.prefix');

        $routeGroups = [];
        foreach ($groups as $group => $items) {
            $routeGroups[] = (object)[
                'name' => ucfirst(trim($group, '/\\')),
                'controller' => explode('@', $items->first()->action['uses'])[0],
                'routes' => $items
            ];
        }

        $array = [];
        foreach ($routeGroups as $group) {
            $items = [];

            /** @var Route $route */
            if (!empty($group->name)) {
                foreach ($group->routes as $route) {
                    $items[] = self::generateRoute($route);
                }

                $array[] = [
                    'name' => $group->name,
                    'item' => $items
                ];

            } else {
                foreach ($group->routes as $route) {
                    $array[] = self::generateRoute($route, true);
                }
            }
        }

        return $array;
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

        return [
            'name' => self::getName($addControllerToName, self::camelCaseToWords($action[1]), $reflectionClass),
            'request' => [
                'method' => ucfirst(self::getActualRouteMethod($route)),
                'header' => [],
                'body' => [],
                'url' => self::getUrl($route),
                'description' => self::generateDoc($reflectionClass, $methodName)
            ]
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

    protected static function getActualRouteMethod(Route $route)
    {
        foreach (self::$allowedMethods as $allowedMethod) {
            if (in_array($allowedMethod, $route->methods)) {
                return $allowedMethod;
            }
        }
    }

    protected static function getUrl(Route $route): array
    {
        $uri = $route->uri;
        $paths = [];
        $variables = [];

        foreach (explode('/', trim($route->uri, '\\/')) as $path) {
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
            'raw' => '{{baseUrl}}' . $uri,
            'host' => '{{baseUrl}}',
            'path' => $paths,
            'variable' => $variables
        ];
    }


    protected static function generateDoc(ReflectionClass $reflectionClass, string $methodName): string
    {
        $formRequestName = match ($methodName) {
            'index' => 'paginationFormRequest',
            'create' => 'createFormRequest',
            'update' => 'updateFormRequest',
            default => null
        };

        if ($formRequestName !== null && $reflectionClass->hasProperty($formRequestName)) {
            $requestClassName = $reflectionClass->getProperty($formRequestName)->getDefaultValue();

            $requestClass = new $requestClassName;

            $fields = self::getDataFromRules($requestClass->rules());
        } else {
            $fields = [];
        }

        return self::generateFieldsDoc($fields);
    }

    protected static function getDataFromRules(array $rules): array
    {
        $data = [];
        foreach ($rules as $key => $rule) {
            $field = [];

            $field['required'] = self::inRule($rule, 'required');

            foreach (self::$dataTypes as $item) {
                if (self::inRule($rule, $item)) {
                    $dataType = $item;
                    $field['type'] = $dataType;
                    break;
                }
            }

            $field['name'] = $key;

            $data[] = $field;
        }
        return $data;
    }

    protected static function inRule($rule, $needle): bool
    {
        if (is_array($rule)) {
            return in_array($needle, $rule);
        }
        if (is_string($rule)) {
            return str_contains($rule, $needle);
        }
        return false;
    }

    protected static function generateFieldsDoc(array $fields): string
    {
        /**
         * Markdown template:
         * - column - required|nullable
         */
        $text = '';
        $i = 0;

        if (count($fields) !== 0) {
            $text .= "##### Fields\n\n";
        }

        foreach ($fields as $field) {
            if ($i !== 0) {
                $text .= "\n";
            }

            $isRequired = $field['required'] ? 'required' : 'nullable';

            $text .= " - {$field['name']} - $isRequired";

            if (!empty($field['type'])) {
                $text .= '|' . $field['type'];
            }

            $i++;
        }

        return $text;
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
}
