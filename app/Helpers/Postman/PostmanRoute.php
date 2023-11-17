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

        $doc = self::generateFieldsDoc($fields);

        if ($reflectionClass->hasProperty('filterable') && !empty($reflectionClass->getProperty('filterable')->getDefaultValue())) {
            $doc .= self::generateFiltersDoc('Filterable columns', $reflectionClass->getProperty('filterable')->getDefaultValue());
        }

        if ($reflectionClass->hasProperty('sortable') && !empty($reflectionClass->getProperty('sortable')->getDefaultValue())) {
            $doc .= self::generateSortDocs($reflectionClass->getProperty('sortable')->getDefaultValue());
        }

        if ($reflectionClass->hasProperty('searchable') && !empty($reflectionClass->getProperty('searchable')->getDefaultValue())) {
            $doc .= self::generateFiltersDoc('Searchable columns', $reflectionClass->getProperty('searchable')->getDefaultValue());
        }

        return $doc;
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

    protected static function generateFiltersDoc(string $title, array $filters): string
    {
        $text = '';
        $i = 0;

        if (count($filters) !== 0) {
            $text .= "\n\n##### $title\n\n";
        }

        foreach ($filters as $column => $type) {
            if ($i !== 0) {
                $text .= "\n";
            }

            $name = strtolower($type->name);
            $text .= " - $column - $name";

            $i++;
        }

        return $text;
    }

    protected static function generateSortDocs(array $sortable): string
    {
        $text = '';
        $i = 0;

        if (count($sortable) !== 0) {
            $text .= "\n\n##### Sortable columns\n\n";
        }

        foreach ($sortable as $column => $type) {
            if ($i !== 0) {
                $text .= "\n";
            }

            $text .= " - $column";

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
