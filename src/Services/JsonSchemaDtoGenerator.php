<?php

namespace BellissimoPizza\RequestDtoGenerator\Services;

use BellissimoPizza\RequestDtoGenerator\Generator\ValidationSchemaGenerator;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Str;
use ReflectionClass;

class JsonSchemaDtoGenerator
{
    protected Filesystem $filesystem;
    protected array $config;

    public function __construct(Filesystem $filesystem, array $config)
    {
        $this->filesystem = $filesystem;
        $this->config = $config;
    }

    /**
     * Generate a DTO from a Request class using JSON Schema approach
     */
    public function generateFromRequest(string $requestClass): string
    {
        $requestReflection = new ReflectionClass($requestClass);
        $requestInstance = $requestReflection->newInstance();
        
        $rules = $this->extractRules($requestInstance);
        $jsonSchema = $this->convertRulesToJsonSchema($rules);
        $properties = $this->analyzeJsonSchema($jsonSchema);
        
        $dtoName = $this->getDtoName($requestClass);
        $dtoContent = $this->generateDtoContent($dtoName, $properties, $rules);
        
        return $dtoContent;
    }

    /**
     * Generate multiple DTOs from a Request class using JSON Schema approach
     */
    public function generateMultipleDtosFromRequest(string $requestClass): array
    {
        $requestReflection = new ReflectionClass($requestClass);
        $requestInstance = $requestReflection->newInstance();
        
        $rules = $this->extractRules($requestInstance);
        $jsonSchema = $this->convertRulesToJsonSchema($rules);
        $properties = $this->analyzeJsonSchema($jsonSchema);
        
        $dtoName = $this->getDtoName($requestClass);
        $generatedDtos = [];
        
        // Generate main DTO
        $generatedDtos[$dtoName] = $this->generateDtoContent($dtoName, $properties, $rules);
        
        // Generate separate DTOs for typed arrays
        if ($this->config['generate_separate_dtos_for_arrays'] ?? false) {
            $separateDtos = $this->generateSeparateDtosForArrays($properties, $dtoName);
            $generatedDtos = array_merge($generatedDtos, $separateDtos);
            
            // Recursively generate DTOs for nested structures
            $this->generateAllNestedDtos($separateDtos, $generatedDtos);
        }
        
        return $generatedDtos;
    }

    /**
     * Get all Request classes from the configured directory
     */
    public function getRequestClasses(): array
    {
        $requestDirectory = $this->config['request_directory'] ?? app_path('Http/Requests');
        
        if (!is_dir($requestDirectory)) {
            return [];
        }

        $requestClasses = [];
        $files = glob($requestDirectory . '/*.php');

        foreach ($files as $file) {
            $className = basename($file, '.php');
            $namespace = $this->getNamespaceFromFile($file);
            
            if ($namespace) {
                $fullClassName = $namespace . '\\' . $className;
                if (class_exists($fullClassName)) {
                    $reflection = new ReflectionClass($fullClassName);
                    if ($reflection->hasMethod('rules')) {
                        $requestClasses[] = $fullClassName;
                    }
                }
            }
        }

        return $requestClasses;
    }

    /**
     * Extract namespace from PHP file
     */
    protected function getNamespaceFromFile(string $file): ?string
    {
        $content = file_get_contents($file);
        if (preg_match('/namespace\s+([^;]+);/', $content, $matches)) {
            return trim($matches[1]);
        }
        return null;
    }

    /**
     * Convert Laravel validation rules to JSON Schema
     */
    protected function convertRulesToJsonSchema(array $rules): array
    {
        $generator = new ValidationSchemaGenerator();
        return $generator->generate($rules);
    }

    /**
     * Build JSON Schema property from Laravel rule
     */
    protected function buildJsonSchemaProperty(string $field, string $rule): ?array
    {
        $ruleArray = explode('|', $rule);
        $isNullable = in_array('nullable', $ruleArray);

        $property = [];

        // Determine type and format
        if (in_array('string', $ruleArray)) {
            $property['type'] = 'string';
            $property['example'] = $this->getStringExample($ruleArray);

        } elseif (in_array('email', $ruleArray)) {
            $property['type'] = 'string';
            $property['format'] = 'email';
            $property['example'] = 'user@example.com';

        } elseif (in_array('uuid', $ruleArray)) {
            $property['type'] = 'string';
            $property['format'] = 'uuid';
            $property['example'] = $this->generateUuid();

        } elseif (in_array('date', $ruleArray)) {
            $property['type'] = 'string';
            $property['format'] = 'date';
            $property['example'] = date('Y-m-d');

        } elseif (in_array('numeric', $ruleArray)) {
            $property['type'] = 'number';
            $property['format'] = 'float';
            $property['example'] = $this->getNumericExample($ruleArray);

        } elseif (in_array('integer', $ruleArray)) {
            $property['type'] = 'integer';
            $property['example'] = $this->getIntegerExample($ruleArray);

        } elseif (in_array('boolean', $ruleArray)) {
            $property['type'] = 'boolean';
            $property['example'] = true;

        } elseif (in_array('array', $ruleArray)) {
            $property['type'] = 'array';
            if (strpos($field, '*') !== false) {
                // This is an array item, return null to skip
                return null;
            }

        } else {
            // For cases like "customer" => "required" (object)
            if (strpos($rule, 'required') !== false && !strpos($field, '*')) {
                $property['type'] = 'object';
                $property['required'] = true;
            } else {
                return null;
            }
        }

        // Add constraints
        foreach ($ruleArray as $r) {
            if (strpos($r, 'max:') === 0) {
                $max = (int)substr($r, 4);
                if ($property['type'] === 'string') {
                    $property['maxLength'] = $max;
                } elseif (in_array($property['type'], ['integer', 'number'])) {
                    $property['maximum'] = $max;
                }
            } elseif (strpos($r, 'min:') === 0) {
                $min = (int)substr($r, 4);
                if ($property['type'] === 'string') {
                    $property['minLength'] = $min;
                } elseif (in_array($property['type'], ['integer', 'number'])) {
                    $property['minimum'] = $min;
                } elseif ($property['type'] === 'array') {
                    $property['minItems'] = $min;
                }
            } elseif (strpos($r, 'in:') === 0) {
                $values = explode(',', substr($r, 3));
                $property['enum'] = $values;
                if (isset($property['example'])) {
                    $property['example'] = $values[0];
                }
            }
        }

        // Add nullable
        if ($isNullable) {
            $property['nullable'] = true;
        }

        return $property;
    }

    /**
     * Set nested property in structure
     */
    protected function setNestedProperty(array &$properties, string $field, array $property): void
    {
        $parts = explode('.', $field);
        
        // Handle array fields (e.g., items.*.productId)
        if (in_array('*', $parts)) {
            $this->setArrayProperty($properties, $parts, $property);
            return;
        }
        
        // Handle regular nested properties (e.g., customer.address.street)
        $current = &$properties;
        for ($i = 0; $i < count($parts); $i++) {
            $part = $parts[$i];
            $isLast = ($i === count($parts) - 1);

            if ($isLast) {
                $current[$part] = $property;
            } else {
                if (!isset($current[$part])) {
                    $current[$part] = [
                        'type' => 'object',
                        'properties' => []
                    ];
                }
                $current = &$current[$part]['properties'];
            }
        }
    }

    /**
     * Set array property (e.g., items.*.productId)
     */
    protected function setArrayProperty(array &$properties, array $parts, array $property): void
    {
        $arrayField = $parts[0]; // e.g., "items"
        
        // Handle different array patterns
        if (count($parts) === 2 && $parts[1] === '*') {
            // Simple array like "tags.*" => "string"
            // Create simple array structure
            if (!isset($properties[$arrayField])) {
                $properties[$arrayField] = [
                    'type' => 'array',
                    'items' => $property
                ];
            }
        } else {
            // Complex array like "items.*.productId"
            $fieldName = $parts[2] ?? 'value';
            
            // Create array structure if it doesn't exist
            if (!isset($properties[$arrayField])) {
                $properties[$arrayField] = [
                    'type' => 'array',
                    'items' => [
                        'type' => 'object',
                        'properties' => []
                    ]
                ];
            }
            
            // Ensure items has type: "object"
            if (!isset($properties[$arrayField]['items']['type'])) {
                $properties[$arrayField]['items']['type'] = 'object';
            }
            
            // Add the field to array items
            $properties[$arrayField]['items']['properties'][$fieldName] = $property;
        }
        
        // Add to required if needed
        if (isset($property['required']) && $property['required']) {
            if (!isset($properties[$arrayField]['items']['required'])) {
                $properties[$arrayField]['items']['required'] = [];
            }
            if (!in_array($fieldName, $properties[$arrayField]['items']['required'])) {
                $properties[$arrayField]['items']['required'][] = $fieldName;
            }
        }
    }

    /**
     * Analyze JSON Schema and convert to properties array
     */
    protected function analyzeJsonSchema(array $jsonSchema): array
    {
        $properties = [];
        
        if (!isset($jsonSchema['properties'])) {
            return $properties;
        }

        foreach ($jsonSchema['properties'] as $field => $schema) {
            $properties[$field] = $this->convertSchemaToProperty($field, $schema);
        }

        return $properties;
    }

    /**
     * Convert JSON Schema property to our property format
     */
    protected function convertSchemaToProperty(string $field, array $schema): array
    {
        $property = [
            'name' => $field,
            'type' => $this->mapJsonSchemaType($schema['type']),
            'nullable' => $schema['nullable'] ?? false,
            'array' => false,
            'nested' => false,
            'structure' => null,
        ];

        // Handle arrays
        if ($schema['type'] === 'array') {
            $property['type'] = 'array';
            $property['array'] = true;
            $property['nested'] = true;
            
            // Check for duplicated structure (like properties.items.items)
            if (isset($schema['properties'][$field]) && 
                $schema['properties'][$field]['type'] === 'array' &&
                isset($schema['properties'][$field]['items'])) {
                
                // Use the nested structure
                $property['structure'] = $schema['properties'][$field];
            } else {
                $property['structure'] = $schema;
            }
            
            // Check for deeply nested duplicated structure (like properties.modifiers.properties.modifiers.items)
            if (isset($schema['properties'][$field]['properties'][$field]) && 
                $schema['properties'][$field]['properties'][$field]['type'] === 'array' &&
                isset($schema['properties'][$field]['properties'][$field]['items'])) {
                
                // Use the deeply nested structure
                $property['structure'] = $schema['properties'][$field]['properties'][$field];
            }

            // Check if this should be a typed array
            if (($this->config['generate_separate_dtos_for_arrays'] ?? false) && 
                isset($property['structure']['items']) && 
                $property['structure']['items']['type'] === 'object') {
                
                $arrayDtoName = $this->getArrayDtoName($field, 'MainDto');
                $property['array_type'] = $arrayDtoName;
                $property['phpdoc_type'] = $arrayDtoName . '[]';
                $property['is_simple_array'] = false;
            } else {
                $property['array_type'] = 'object';
                $property['phpdoc_type'] = 'array';
                $property['is_simple_array'] = false;
            }
            
            // Check if this is a simple array (like tags.* => string)
            if (isset($property['structure']['items']) && 
                $property['structure']['items']['type'] !== 'object' && 
                !isset($property['structure']['items']['properties'])) {
                
                // This is a simple array, create a DTO for it
                if ($this->config['generate_separate_dtos_for_arrays'] ?? false) {
                    $arrayDtoName = $this->getArrayDtoName($field, 'MainDto');
                    $property['array_type'] = $arrayDtoName;
                    $property['phpdoc_type'] = $arrayDtoName . '[]';
                    $property['is_simple_array'] = true;
                } else {
                    $property['array_type'] = 'object';
                    $property['phpdoc_type'] = 'array';
                    $property['is_simple_array'] = false;
                }
            }
        }
        // Handle objects
        elseif ($schema['type'] === 'object') {
            $property['type'] = 'array';
            $property['nested'] = true;
            $property['structure'] = $schema;
            $property['array_type'] = 'object';
            $property['phpdoc_type'] = 'array';
            
            // Check if this object should generate a separate DTO
            if (($this->config['generate_separate_dtos_for_arrays'] ?? false) && 
                isset($schema['properties']) && 
                !empty($schema['properties'])) {
                
                $objectDtoName = $this->getArrayDtoName($field, 'MainDto');
                $property['array_type'] = $objectDtoName;
                $property['phpdoc_type'] = $objectDtoName;
                $property['array'] = false; // This is an object, not an array
            }
        }

        return $property;
    }

    /**
     * Map JSON Schema type to PHP type
     */
    protected function mapJsonSchemaType(string $type): string
    {
        return match($type) {
            'string' => 'string',
            'integer' => 'int',
            'number' => 'int|float',
            'boolean' => 'bool',
            'array' => 'array',
            'object' => 'array',
            default => 'mixed'
        };
    }

    /**
     * Generate separate DTOs for arrays with nested structures
     */
    protected function generateSeparateDtosForArrays(array $properties, string $mainDtoName): array
    {
        $separateDtos = [];
        
        foreach ($properties as $property) {
            if (isset($property['nested']) && $property['nested'] && isset($property['structure'])) {
                $isDirectArray = $this->isDirectArray($property['structure']);
                $isObject = $property['structure']['type'] === 'object';
                
                if ($isDirectArray || $isObject) {
                    $arrayDtoName = $this->getArrayDtoName($property['name'], $mainDtoName);
                    
                    if ($isDirectArray) {
                        $arrayProperties = $this->extractArrayItemPropertiesFromSchema($property['structure']);
                    } else {
                        // For objects, extract properties directly
                        $arrayProperties = $this->extractArrayItemPropertiesFromSchema($property['structure']);
                    }
                    
                    if (!empty($arrayProperties)) {
                        $separateDtos[$arrayDtoName] = $this->generateDtoContent($arrayDtoName, $arrayProperties, []);
                        
                        // Recursively generate DTOs for nested objects
                        $nestedDtos = $this->generateSeparateDtosForArrays($arrayProperties, $arrayDtoName);
                        $separateDtos = array_merge($separateDtos, $nestedDtos);
                    }
                }
            }
            // Handle properties extracted from DTO content (they have array_type but no structure)
            elseif (isset($property['array_type']) && $property['array_type'] !== 'object' && str_ends_with($property['array_type'], 'Dto')) {
                $arrayDtoName = $property['array_type'];
                
                // Create a simple DTO with empty properties (will be filled by recursive calls)
                $separateDtos[$arrayDtoName] = $this->generateDtoContent($arrayDtoName, [], []);
            }
        }
        
        return $separateDtos;
    }

    /**
     * Recursively generate all nested DTOs
     */
    protected function generateAllNestedDtos(array $separateDtos, array &$generatedDtos): void
    {
        $newDtos = [];
        
        foreach ($separateDtos as $dtoName => $dtoContent) {
            // Parse the DTO content to extract properties
            $properties = $this->extractPropertiesFromDtoContent($dtoContent);
            
            if (!empty($properties)) {
                $nestedDtos = $this->generateSeparateDtosForArrays($properties, $dtoName);
                
                foreach ($nestedDtos as $nestedDtoName => $nestedDtoContent) {
                    if (!isset($generatedDtos[$nestedDtoName])) {
                        $generatedDtos[$nestedDtoName] = $nestedDtoContent;
                        $newDtos[$nestedDtoName] = $nestedDtoContent;
                    }
                }
            }
        }
        
        // Recursively generate DTOs for newly created DTOs
        if (!empty($newDtos)) {
            $this->generateAllNestedDtos($newDtos, $generatedDtos);
        }
    }

    /**
     * Extract properties from DTO content
     */
    protected function extractPropertiesFromDtoContent(string $dtoContent): array
    {
        $properties = [];
        
        // Look for constructor parameters
        if (preg_match('/public function __construct\((.*?)\)/s', $dtoContent, $matches)) {
            $params = $matches[1];
            
            // Extract parameter types and names
            if (preg_match_all('/(\w+)\s+\$(\w+)/', $params, $paramMatches, PREG_SET_ORDER)) {
                foreach ($paramMatches as $match) {
                    $type = $match[1];
                    $name = $match[2];
                    
                    // Check if this is a DTO type (ends with 'Dto')
                    if (str_ends_with($type, 'Dto')) {
                        // For now, we'll create a simple structure
                        // In a real implementation, you might want to parse the DTO content more thoroughly
                        $properties[$name] = [
                            'name' => $name,
                            'type' => 'array',
                            'array' => false,
                            'nested' => true,
                            'array_type' => $type,
                            'phpdoc_type' => $type,
                            'structure' => [
                                'type' => 'object',
                                'properties' => [
                                    // Add some default properties for common DTOs
                                    'id' => ['type' => 'string'],
                                    'name' => ['type' => 'string']
                                ]
                            ]
                        ];
                    }
                }
            }
        }
        
        return $properties;
    }

    /**
     * Check if structure is a direct array (not an object containing arrays)
     */
    protected function isDirectArray(array $structure): bool
    {
        return $structure['type'] === 'array' && 
               isset($structure['items']) && 
               $structure['items']['type'] === 'object' &&
               isset($structure['items']['properties']) &&
               !empty($structure['items']['properties']);
    }

    /**
     * Extract properties for array items from JSON Schema
     */
    protected function extractArrayItemPropertiesFromSchema(array $structure): array
    {
        $properties = [];
        
        // For arrays, look for items.properties
        if (isset($structure['items']['properties'])) {
            foreach ($structure['items']['properties'] as $field => $schema) {
                $properties[$field] = $this->convertSchemaToProperty($field, $schema);
            }
        }
        // For objects, look for properties directly
        elseif (isset($structure['properties'])) {
            foreach ($structure['properties'] as $field => $schema) {
                $properties[$field] = $this->convertSchemaToProperty($field, $schema);
            }
        }

        return $properties;
    }

    /**
     * Get array DTO name
     */
    protected function getArrayDtoName(string $arrayFieldName, string $mainDtoName): string
    {
        return Str::studly($arrayFieldName) . 'Dto';
    }

    /**
     * Generate string example based on rules
     */
    protected function getStringExample(array $rules): string
    {
        $maxLength = 255;
        foreach ($rules as $rule) {
            if (strpos($rule, 'max:') === 0) {
                $maxLength = (int)substr($rule, 4);
                break;
            }
        }

        $examples = [
            10 => '12345',
            20 => '+1234567890',
            50 => 'Example text',
            100 => 'This is a longer example text',
            255 => 'This is a very long example text that demonstrates what a longer string might look like',
            500 => 'This is an even longer example text for instructions or notes fields',
            1000 => 'This is an extremely long example text for detailed descriptions or comments'
        ];

        foreach ([10, 20, 50, 100, 255, 500, 1000] as $length) {
            if ($maxLength <= $length) {
                return $examples[$length];
            }
        }

        return 'Example text';
    }

    /**
     * Generate numeric example
     */
    protected function getNumericExample(array $rules): float
    {
        $min = 0;
        foreach ($rules as $rule) {
            if (strpos($rule, 'min:') === 0) {
                $min = (float)substr($rule, 4);
                break;
            }
        }
        return $min > 0 ? $min * 10 : 99.99;
    }

    /**
     * Generate integer example
     */
    protected function getIntegerExample(array $rules): int
    {
        $min = 0;
        foreach ($rules as $rule) {
            if (strpos($rule, 'min:') === 0) {
                $min = (int)substr($rule, 4);
                break;
            }
        }
        return $min > 0 ? $min : 1;
    }

    /**
     * Check if field is nullable
     */
    protected function isNullable(string $rule): bool
    {
        return strpos($rule, 'nullable') !== false;
    }

    /**
     * Generate UUID
     */
    protected function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Extract rules from request instance
     */
    protected function extractRules($requestInstance): array
    {
        return $requestInstance->rules();
    }

    /**
     * Get DTO name from request class
     */
    protected function getDtoName(string $requestClass): string
    {
        $className = class_basename($requestClass);
        return str_replace('Request', 'Dto', $className);
    }

    /**
     * Generate DTO content
     */
    protected function generateDtoContent(string $dtoName, array $properties, array $rules): string
    {
        $namespace = $this->config['dto_namespace'];
        $baseClass = $this->config['dto_base_class'];
        $imports = $this->getRequiredImports($properties);
        
        $content = "<?php\n\n";
        $content .= "namespace {$namespace};\n\n";
        $content .= "use {$baseClass};\n";
        
        foreach ($imports as $import) {
            $content .= "use {$import};\n";
        }
        
        $content .= "class {$dtoName} extends BaseDto\n";
        $content .= "{\n";
        
        if ($this->config['generate_constructor'] ?? true) {
            if ($this->config['constructor_property_promotion'] ?? true) {
                $content .= $this->generateConstructorWithPropertyPromotion($properties);
            } else {
                $content .= $this->generateConstructor($properties);
            }
        }
        
        // Always generate fromArray method
        $content .= $this->generateFromArrayMethod($properties);
        
        if ($this->config['generate_accessors'] ?? true) {
            foreach ($properties as $property) {
                $content .= $this->generateAccessors($property);
            }
        }
        
        $content .= "}\n";
        
        return $content;
    }

    /**
     * Get required imports for typed arrays
     */
    protected function getRequiredImports(array $properties): array
    {
        $imports = [];
        $namespace = $this->config['dto_namespace'];
        
        foreach ($properties as $property) {
            if (isset($property['array_type']) && $property['array_type'] !== 'object') {
                $import = $namespace . '\\' . $property['array_type'];
                if (!in_array($import, $imports)) {
                    $imports[] = $import;
                }
            }
        }
        
        return $imports;
    }

    /**
     * Format nullable type correctly for PHP
     */
    protected function formatNullableType(string $type, bool $nullable): string
    {
        if (!$nullable) {
            return $type;
        }
        
        // For union types, add |null instead of ? prefix
        if (str_contains($type, '|')) {
            return $type . '|null';
        }
        
        // For single types, use ? prefix
        return '?' . $type;
    }

    /**
     * Generate constructor with property promotion
     */
    protected function generateConstructorWithPropertyPromotion(array $properties): string
    {
        $content = "\n    /**\n";
        
        // Add PHPDoc parameters for all properties
        foreach ($properties as $property) {
            if (isset($property['phpdoc_type']) && $property['phpdoc_type'] !== $property['type']) {
                $content .= "     * @param {$property['phpdoc_type']} \${$property['name']}\n";
            } else {
                $phpdocType = $this->formatNullableType($property['type'], $property['nullable']);
                $content .= "     * @param {$phpdocType} \${$property['name']}\n";
            }
        }
        
        $content .= "     */\n";
        $content .= "    public function __construct(\n";
        
        $propertyCount = count($properties);
        $currentIndex = 0;
        $visibility = $this->config['property_visibility'] ?? 'private';
        
        // Sort properties: required first, then optional
        $requiredProperties = [];
        $optionalProperties = [];
        
        foreach ($properties as $property) {
            if ($property['nullable']) {
                $optionalProperties[] = $property;
            } else {
                $requiredProperties[] = $property;
            }
        }
        
        $sortedProperties = array_merge($requiredProperties, $optionalProperties);
        
        foreach ($sortedProperties as $property) {
            $isLast = (++$currentIndex === $propertyCount);
            
            // Use array_type for objects, type for primitives
            $type = $property['array'] ? $property['type'] : ($property['array_type'] ?? $property['type']);
            $default = $property['nullable'] ? ' = null' : '';
            $comma = $isLast ? '' : ',';
            
            $formattedType = $this->formatNullableType($type, $property['nullable']);
            $readonly = $this->config['readonly_properties'] ? 'readonly ' : '';
            
            $content .= "        {$visibility} {$readonly}{$formattedType} \${$property['name']}{$default}{$comma}\n";
        }
        
        $content .= "    ) {}\n";
        
        return $content;
    }

    /**
     * Generate constructor
     */
    protected function generateConstructor(array $properties): string
    {
        $content = "\n    /**\n";
        $content .= "     * Constructor\n";
        $content .= "     */\n";
        $content .= "    public function __construct(\n";
        
        $constructorParams = [];
        foreach ($properties as $property) {
            $type = $property['type'];
            $default = $property['nullable'] ? ' = null' : '';
            $formattedType = $this->formatNullableType($type, $property['nullable']);
            $constructorParams[] = "        {$formattedType} \${$property['name']}{$default}";
        }
        $content .= implode(",\n", $constructorParams);
        $content .= "\n    ) {\n";
        foreach ($properties as $property) {
            $content .= "        \$this->{$property['name']} = \${$property['name']};\n";
        }
        $content .= "    }\n";
        
        return $content;
    }

    /**
     * Generate accessors
     */
    protected function generateAccessors(array $property): string
    {
        $name = $property['name'];
        $studlyName = Str::studly($name);
        $readonly = $this->config['readonly_properties'];
        
        // Use array_type for objects, type for primitives
        $type = $property['array'] ? $property['type'] : ($property['array_type'] ?? $property['type']);
        
        $content = "\n    /**\n";
        
        // Add PHPDoc return type for typed arrays
        if (isset($property['phpdoc_type']) && $property['phpdoc_type'] !== $type) {
            $content .= "     * @return {$property['phpdoc_type']}\n";
        } else {
            // Add PHPDoc return type for regular properties
            $phpdocType = $this->formatNullableType($type, $property['nullable']);
            $content .= "     * @return {$phpdocType}\n";
        }
        
        $content .= "     */\n";
        $formattedType = $this->formatNullableType($type, $property['nullable']);
        $content .= "    public function get{$studlyName}(): {$formattedType}\n";
        $content .= "    {\n";
        $content .= "        return \$this->{$name};\n";
        $content .= "    }\n";
        
        // Only generate setter if properties are not readonly
        if (!$readonly) {
            $content .= "\n    /**\n";
            $formattedType = $this->formatNullableType($type, $property['nullable']);
            $content .= "     * @param {$formattedType} \${$name}\n";
            $content .= "     */\n";
            $content .= "    public function set{$studlyName}({$formattedType} \${$name}): void\n";
            $content .= "    {\n";
            $content .= "        \$this->{$name} = \${$name};\n";
            $content .= "    }\n";
        }
        
        return $content;
    }

    /**
     * Generate fromArray method with direct constructor parameters
     */
    protected function generateFromArrayMethod(array $properties): string
    {
        $content = "\n    /**\n";
        $content .= "     * Create DTO from array\n";
        $content .= "     *\n";
        $content .= "     * @param array \$data\n";
        $content .= "     * @return static\n";
        $content .= "     */\n";
        $content .= "    public static function fromArray(array \$data): static\n";
        $content .= "    {\n";
        $content .= "        return new static(\n";
        
        // Sort properties: required first, then optional
        $requiredProperties = [];
        $optionalProperties = [];
        
        foreach ($properties as $property) {
            if ($property['nullable']) {
                $optionalProperties[] = $property;
            } else {
                $requiredProperties[] = $property;
            }
        }
        
        $sortedProperties = array_merge($requiredProperties, $optionalProperties);
        $propertyCount = count($sortedProperties);
        $currentIndex = 0;
        
        foreach ($sortedProperties as $property) {
            $isLast = (++$currentIndex === $propertyCount);
            $name = $property['name'];
            $comma = $isLast ? '' : ',';
            $nullable = $property['nullable'] ?? false;
            
            // Handle DTOs (both arrays and objects)
            if (isset($property['array_type']) && $property['array_type'] !== 'object') {
                $dtoClassName = $property['array_type'];
                
                // Check if this is an array of DTOs
                if ($property['array']) {
                    // Check if this is a simple array (like tags.* => string)
                    if (isset($property['is_simple_array']) && $property['is_simple_array']) {
                        $content .= "            {$name}: array_map(fn(\$item) => {$dtoClassName}::fromArray(['value' => \$item]), \$data['{$name}'] ?? []){$comma}\n";
                    } else {
                        $content .= "            {$name}: array_map(fn(\$item) => {$dtoClassName}::fromArray(\$item), \$data['{$name}'] ?? []){$comma}\n";
                    }
                } else {
                    // This is a single DTO object
                    if ($nullable) {
                        $content .= "            {$name}: isset(\$data['{$name}']) ? {$dtoClassName}::fromArray(\$data['{$name}']) : null{$comma}\n";
                    } else {
                        $content .= "            {$name}: {$dtoClassName}::fromArray(\$data['{$name}']){$comma}\n";
                    }
                }
            } else {
                // Handle primitive fields
                if ($nullable) {
                    $content .= "            {$name}: \$data['{$name}'] ?? null{$comma}\n";
                } else {
                    $content .= "            {$name}: \$data['{$name}']{$comma}\n";
                }
            }
        }
        
        $content .= "        );\n";
        $content .= "    }\n";
        
        return $content;
    }
}
