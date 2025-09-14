<?php

return [
    /*
    |--------------------------------------------------------------------------
    | DTO Namespace
    |--------------------------------------------------------------------------
    |
    | The namespace where generated DTOs will be placed.
    |
    */
    'dto_namespace' => 'App\\DTOs',

    /*
    |--------------------------------------------------------------------------
    | DTO Directory
    |--------------------------------------------------------------------------
    |
    | The directory where generated DTOs will be saved.
    |
    */
    'dto_directory' => app_path('DTOs'),

    /*
    |--------------------------------------------------------------------------
    | Request Directory
    |--------------------------------------------------------------------------
    |
    | The directory where Request files are located.
    |
    */
    'request_directory' => app_path('Http/Requests'),

    /*
    |--------------------------------------------------------------------------
    | DTO Base Class
    |--------------------------------------------------------------------------
    |
    | The base class that all generated DTOs will extend.
    |
    */
    'dto_base_class' => 'BellissimoPizza\\RequestDtoGenerator\\BaseDto',

    /*
    |--------------------------------------------------------------------------
    | Auto-generate Properties
    |--------------------------------------------------------------------------
    |
    | Whether to automatically generate properties from validation rules.
    |
    */
    'auto_generate_properties' => true,

    /*
    |--------------------------------------------------------------------------
    | Include Validation Rules
    |--------------------------------------------------------------------------
    |
    | Whether to include validation rules as comments in the generated DTO.
    |
    */
    'include_validation_rules' => true,

    /*
    |--------------------------------------------------------------------------
    | Generate Constructor
    |--------------------------------------------------------------------------
    |
    | Whether to generate a constructor with all properties.
    |
    */
    'generate_constructor' => true,

    /*
    |--------------------------------------------------------------------------
    | Generate Getters and Setters
    |--------------------------------------------------------------------------
    |
    | Whether to generate getter and setter methods for properties.
    |
    */
    'generate_accessors' => true,

    /*
    |--------------------------------------------------------------------------
    | Readonly Properties
    |--------------------------------------------------------------------------
    |
    | Whether to make DTO properties readonly for immutability.
    | When enabled, properties will be readonly and setters won't be generated.
    |
    */
    'readonly_properties' => true,

    /*
    |--------------------------------------------------------------------------
    | Constructor Property Promotion
    |--------------------------------------------------------------------------
    |
    | Whether to use constructor property promotion (PHP 8+ feature).
    | When enabled, properties are declared as constructor parameters with
    | private readonly visibility instead of as class properties.
    |
    */
    'constructor_property_promotion' => true,

    /*
    |--------------------------------------------------------------------------
    | Property Visibility
    |--------------------------------------------------------------------------
    |
    | The visibility of properties when using constructor property promotion.
    | Options: 'private' or 'public'
    | 
    | - 'private': Properties are private readonly, access only through getters
    | - 'public': Properties are public readonly, direct access allowed
    |
    */
    'property_visibility' => 'private',

    /*
    |--------------------------------------------------------------------------
    | Generate Separate DTOs for Arrays
    |--------------------------------------------------------------------------
    |
    | Whether to generate separate DTO classes for array items with nested structures.
    | When enabled, arrays like 'items.*.productId' will generate an ItemDto class
    | and the property will be typed as ItemDto[] instead of just array.
    |
    */
    'generate_separate_dtos_for_arrays' => true,
];
