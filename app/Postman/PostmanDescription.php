<?php

namespace App\Postman;

use ReflectionClass;
use ReflectionException;

class PostmanDescription
{
    public function __construct(
        protected ReflectionClass $controllerClass,
        protected string          $methodName,
        protected object|null     $requestClass = null
    )
    {
    }

    /**
     * @throws ReflectionException
     */
    public function toArray(): string
    {
        $fields = [];

        if (!empty($this->requestClass)) {
            $fields = self::getDataFromRules($this->requestClass->rules());
        }

        $doc = self::generateFieldsDoc($fields);

        if (in_array($this->methodName, ['getAll', 'getOne'])) {

            if ($this->controllerClass->hasProperty('filterable') && !empty($this->controllerClass->getProperty('filterable')->getDefaultValue())) {
                $doc .= self::generateFiltersDoc('Filterable columns', $this->controllerClass->getProperty('filterable')->getDefaultValue());
            }

            if ($this->controllerClass->hasProperty('sortable')
                && !empty($this->controllerClass->getProperty('sortable')->getDefaultValue())) {
                $doc .= self::generateSortDocs($this->controllerClass->getProperty('sortable')->getDefaultValue());
            }

            if ($this->controllerClass->hasProperty('searchable') && !empty($this->controllerClass->getProperty('searchable')->getDefaultValue())) {
                $doc .= self::generateFiltersDoc('Searchable columns', $this->controllerClass->getProperty('searchable')->getDefaultValue());
            }
        }

        return $doc;
    }

    protected static function getDataFromRules(array $rules): array
    {
        $data = [];
        foreach ($rules as $key => $rule) {
            $field = [];

            $field['required'] = self::inRule($rule, 'required');

            foreach (PostmanRoute::$dataTypes as $item) {
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
}
