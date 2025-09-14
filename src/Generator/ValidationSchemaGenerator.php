<?php

namespace BellissimoPizza\RequestDtoGenerator\Generator;

class ValidationSchemaGenerator
{
    public function generate(array $rules): array
    {
        $schema = [
            'type'       => 'object',
            'properties' => [],
        ];

        foreach ($rules as $field => $ruleSet) {
            $this->applyRule($schema, explode('.', $field), $ruleSet);
        }

        return $schema;
    }

    private function applyRule(array &$schema, array $path, string|array $rules): void
    {
        $current = array_shift($path);

        if (is_array($rules)) {
            $rules = implode('|', $rules);
        }

        // массив
        if ($current === '*') {
            if (!isset($schema['items'])) {
                $schema['type']  = 'array';
                $schema['items'] = [
                    'type'       => 'object',
                    'properties' => [],
                ];
            }

            $this->applyRule($schema['items'], $path, $rules);
            return;
        }

        // конечное поле
        if (empty($path)) {
            $schema['properties'][$current] = $this->mapRules($rules);
            return;
        }

        // вложенный объект
        if (!isset($schema['properties'][$current])) {
            $schema['properties'][$current] = [
                'type'       => 'object',
                'properties' => [],
            ];
        }

        $this->applyRule($schema['properties'][$current], $path, $rules);
    }

    private function mapRules(string $rules): array
    {
        $result = [
            'type'     => 'string',
            'nullable' => false,
        ];

        foreach (explode('|', $rules) as $rule) {
            $rule = trim($rule);

            switch (true) {
                case $rule === 'required':
                    $result['nullable'] = false;
                    break;
                case $rule === 'nullable':
                    $result['nullable'] = true;
                    break;
                case $rule === 'string':
                case str_starts_with($rule, 'uuid'):
                case str_starts_with($rule, 'email'):
                    $result['type'] = 'string';
                    break;
                case $rule === 'integer':
                    $result['type'] = 'integer';
                    break;
                case $rule === 'numeric':
                    $result['type'] = 'number';
                    break;
                case $rule === 'boolean':
                    $result['type'] = 'boolean';
                    break;
                 case $rule === 'array':
                     $result['type']  = 'array';
                     $result['items'] = ['type' => 'object', 'properties' => []];
                     break;
                case $rule === 'date':
                case str_starts_with($rule, 'date_format'):
                    $result['type']   = 'string';
                    $result['format'] = 'date-time';
                    break;
            }
        }

        return $result;
    }
}
