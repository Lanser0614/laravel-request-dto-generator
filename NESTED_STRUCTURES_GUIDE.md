# Nested Structures Guide for Laravel Request DTO Generator

## Overview

The Laravel Request DTO Generator now supports complex nested structures from Laravel validation rules. This allows you to generate DTOs that handle deeply nested data structures like those found in API requests.

## Supported Nested Structures

### 1. Simple Nested Objects

**Request Rules:**
```php
public function rules(): array
{
    return [
        'user.name' => 'required|string|max:255',
        'user.email' => 'required|email',
        'user.profile.age' => 'required|integer|min:18',
        'user.profile.city' => 'nullable|string|max:100',
    ];
}
```

**Generated DTO:**
```php
class CreateUserDto extends BaseDto
{
    public function __construct(
        private readonly array $user, // Nested structure
        // ... other properties
    ) {}
}
```

### 2. Arrays with Nested Objects

**Request Rules:**
```php
public function rules(): array
{
    return [
        'items' => 'required|array',
        'items.*.productId' => 'required|uuid',
        'items.*.quantity' => 'required|integer|min:1',
        'items.*.price' => 'required|numeric',
        'items.*.modifiers' => 'nullable|array',
        'items.*.modifiers.*.id' => 'required|uuid',
        'items.*.modifiers.*.name' => 'required|string',
    ];
}
```

**Generated DTO:**
```php
class CreateOrderDto extends BaseDto
{
    public function __construct(
        private readonly array $items, // Array of nested objects
        // ... other properties
    ) {}
}
```

### 3. Complex Multi-Level Nested Structures

**Request Rules:**
```php
public function rules(): array
{
    return [
        'order.deliveryPoint.coordinates.latitude' => 'required|numeric',
        'order.deliveryPoint.coordinates.longitude' => 'required|numeric',
        'order.deliveryPoint.address.street.name' => 'required|string',
        'order.deliveryPoint.address.street.city' => 'required|string',
        'order.deliveryPoint.address.house' => 'required|string',
        'order.items.*.productId' => 'required|uuid',
        'order.items.*.modifiers.*.id' => 'required|uuid',
    ];
}
```

**Generated DTO:**
```php
class CreateDeliveryDto extends BaseDto
{
    public function __construct(
        private readonly array $order, // Complex nested structure
        // ... other properties
    ) {}
}
```

## How It Works

### 1. Field Analysis

The package analyzes validation rules and identifies nested fields by looking for dots (`.`) in field names:

- `user.name` → Nested field under `user`
- `order.items.*.productId` → Array field under `order.items`
- `profile.address.street.name` → Deeply nested field

### 2. Structure Building

The package builds a nested structure tree:

```php
[
    'user' => [
        'fields' => [
            'name' => ['type' => 'string', 'nullable' => false],
            'email' => ['type' => 'string', 'nullable' => false],
            'profile' => [
                'fields' => [
                    'age' => ['type' => 'int', 'nullable' => false],
                    'city' => ['type' => 'string', 'nullable' => true],
                ]
            ]
        ]
    ]
]
```

### 3. DTO Generation

Based on the structure, the package generates:

- **Array properties** for nested structures
- **Proper nullable types** based on validation rules
- **Documentation comments** describing the nested structure

## Configuration Options

### Property Visibility

```php
// config/request-dto-generator.php
'property_visibility' => 'private', // Default: private readonly
'property_visibility' => 'public',  // Alternative: public readonly
```

### Constructor Property Promotion

```php
'constructor_property_promotion' => true,  // Default: enabled
'constructor_property_promotion' => false, // Alternative: traditional properties
```

### Include Validation Rules

```php
'include_validation_rules' => true,  // Include validation rules in comments
'include_validation_rules' => false, // Skip validation rules
```

## Generated Code Examples

### With Constructor Property Promotion (Default)

```php
class CreateUserDto extends BaseDto
{
    public function __construct(
        private readonly array $user,
        private readonly ?array $profile = null,
        private readonly array $items,
    ) {}
}
```

### Without Constructor Property Promotion

```php
class CreateUserDto extends BaseDto
{
    /**
     * Nested structure: name: string, email: string, profile: {age: int, city: ?string}
     */
    public readonly array $user;

    /**
     * Nested structure: points: {spent: int}
     */
    public readonly ?array $profile;

    public function __construct(
        array $user,
        ?array $profile = null,
        array $items,
    ) {
        $this->user = $user;
        $this->profile = $profile;
        $this->items = $items;
    }
}
```

## Usage Examples

### 1. Basic Nested Structure

```php
// Request
class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'email' => 'required|email',
            'profile.age' => 'required|integer',
            'profile.city' => 'nullable|string',
        ];
    }
}

// Generated DTO
class CreateUserDto extends BaseDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly array $profile,
    ) {}
}

// Usage
$dto = new CreateUserDto(
    name: 'John Doe',
    email: 'john@example.com',
    profile: [
        'age' => 30,
        'city' => 'New York'
    ]
);
```

### 2. Array of Nested Objects

```php
// Request
class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.productId' => 'required|uuid',
            'items.*.quantity' => 'required|integer',
            'items.*.price' => 'required|numeric',
        ];
    }
}

// Generated DTO
class CreateOrderDto extends BaseDto
{
    public function __construct(
        private readonly array $items,
    ) {}
}

// Usage
$dto = new CreateOrderDto(
    items: [
        [
            'productId' => '123e4567-e89b-12d3-a456-426614174000',
            'quantity' => 2,
            'price' => 29.99
        ],
        [
            'productId' => '987fcdeb-51a2-43d7-8f9e-123456789abc',
            'quantity' => 1,
            'price' => 15.50
        ]
    ]
);
```

### 3. Complex Multi-Level Structure

```php
// Request
class CreateDeliveryRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'order.deliveryPoint.coordinates.latitude' => 'required|numeric',
            'order.deliveryPoint.coordinates.longitude' => 'required|numeric',
            'order.deliveryPoint.address.street.name' => 'required|string',
            'order.deliveryPoint.address.street.city' => 'required|string',
            'order.items.*.productId' => 'required|uuid',
            'order.items.*.modifiers.*.id' => 'required|uuid',
        ];
    }
}

// Generated DTO
class CreateDeliveryDto extends BaseDto
{
    public function __construct(
        private readonly array $order,
    ) {}
}

// Usage
$dto = new CreateDeliveryDto(
    order: [
        'deliveryPoint' => [
            'coordinates' => [
                'latitude' => 40.7128,
                'longitude' => -74.0060
            ],
            'address' => [
                'street' => [
                    'name' => 'Broadway',
                    'city' => 'New York'
                ]
            ]
        ],
        'items' => [
            [
                'productId' => '123e4567-e89b-12d3-a456-426614174000',
                'modifiers' => [
                    ['id' => 'mod-1'],
                    ['id' => 'mod-2']
                ]
            ]
        ]
    ]
);
```

## Benefits

### 1. Type Safety

- **Array types** for nested structures
- **Nullable types** based on validation rules
- **Proper type hints** for better IDE support

### 2. Documentation

- **Nested structure descriptions** in comments
- **Validation rules** included in documentation
- **Clear field descriptions** for complex structures

### 3. Flexibility

- **Supports any nesting level** (limited only by PHP)
- **Handles arrays of objects** with `*` notation
- **Preserves nullable information** from validation rules

### 4. Performance

- **Efficient structure analysis** during generation
- **Minimal runtime overhead** for nested data
- **Optimized for large nested structures**

## Limitations

### 1. Array Types Only

Currently, nested structures are always generated as `array` type. This is because:

- PHP doesn't have built-in support for typed arrays
- Laravel validation rules don't specify exact object types
- Arrays provide maximum flexibility for nested data

### 2. No Custom Object Classes

The package doesn't generate separate DTO classes for nested structures. This is by design to:

- Keep the generation simple and fast
- Avoid circular dependencies
- Maintain compatibility with existing code

### 3. Limited Type Information

While the package detects basic types from validation rules, it doesn't:

- Generate custom type classes for nested objects
- Provide runtime type checking for nested structures
- Support complex type unions for nested fields

## Best Practices

### 1. Use Descriptive Field Names

```php
// Good
'user.profile.personalInfo.firstName' => 'required|string'

// Avoid
'u.p.p.fn' => 'required|string'
```

### 2. Keep Nesting Reasonable

```php
// Good - 3-4 levels max
'order.deliveryPoint.address.street.name' => 'required|string'

// Avoid - too deep
'order.deliveryPoint.address.street.building.floor.room.door.number' => 'required|string'
```

### 3. Use Consistent Structure

```php
// Good - consistent structure
'items.*.productId' => 'required|uuid',
'items.*.quantity' => 'required|integer',
'items.*.price' => 'required|numeric',

// Avoid - inconsistent structure
'items.*.productId' => 'required|uuid',
'items.*.details.name' => 'required|string', // Different structure
```

### 4. Document Complex Structures

```php
// Add comments in your Request class
class CreateOrderRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     * 
     * Structure:
     * - items: Array of order items
     *   - productId: UUID of the product
     *   - quantity: Number of items
     *   - modifiers: Optional array of modifiers
     */
    public function rules(): array
    {
        return [
            'items' => 'required|array',
            'items.*.productId' => 'required|uuid',
            'items.*.quantity' => 'required|integer',
            'items.*.modifiers' => 'nullable|array',
            'items.*.modifiers.*.id' => 'required|uuid',
        ];
    }
}
```

## Troubleshooting

### Common Issues

1. **Missing nested fields**: Ensure all nested fields are properly defined in validation rules
2. **Incorrect nullable types**: Check that `nullable` rule is properly set for optional fields
3. **Array type confusion**: Remember that nested structures are always `array` type

### Debug Tips

1. **Check generated DTO**: Review the generated DTO to ensure structure is correct
2. **Validate input data**: Ensure your input data matches the expected nested structure
3. **Use type hints**: Leverage IDE type hints for better development experience

## Conclusion

Nested structures support in Laravel Request DTO Generator provides a powerful way to handle complex data structures while maintaining type safety and code clarity. By following the best practices outlined in this guide, you can effectively use nested structures in your Laravel applications.
