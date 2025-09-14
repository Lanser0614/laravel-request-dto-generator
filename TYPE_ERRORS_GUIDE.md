# Type Errors Guide for Laravel Request DTO Generator

## Overview

When using DTOs with improved typing, you may encounter various types of errors when passing incorrect data types. This guide will help you understand what errors occur and how to avoid them.

## Types of Errors

### 1. TypeError when Creating DTO

**When it occurs:** When passing incorrect type to DTO constructor.

**Error examples:**

#### Passing string to numeric field (int|float)
```php
// ❌ Error
$dto = new CreateProductDto(
    price: "not a number",  // string instead of int|float
    // ... other parameters
);

// Error:
// TypeError: CreateProductDto::__construct(): 
// Argument #1 ($price) must be of type int|float, string given
```

#### Passing array to int field
```php
// ❌ Error
$dto = new CreateProductDto(
    price: 100.50,
    quantity: [1, 2, 3],  // array instead of int
    // ... other parameters
);

// Error:
// TypeError: CreateProductDto::__construct(): 
// Argument #2 ($quantity) must be of type int, array given
```

#### Passing number to string field
```php
// ❌ Error
$dto = new CreateProductDto(
    price: 100.50,
    quantity: 10,
    name: 123,  // int instead of string
    // ... other parameters
);

// Error:
// TypeError: CreateProductDto::__construct(): 
// Argument #3 ($name) must be of type string, int given
```

#### Passing string to bool field
```php
// ❌ Error
$dto = new CreateProductDto(
    price: 100.50,
    quantity: 10,
    name: "Product",
    is_active: "yes",  // string instead of bool
    // ... other parameters
);

// Error:
// TypeError: CreateProductDto::__construct(): 
// Argument #4 ($is_active) must be of type bool, string given
```

#### Passing wrong type to nullable field
```php
// ❌ Error
$dto = new CreateProductDto(
    price: 100.50,
    quantity: 10,
    name: "Product",
    is_active: true,
    discount: "discount"  // string instead of int|float|null
    // ... other parameters
);

// Error:
// TypeError: CreateProductDto::__construct(): 
// Argument #5 ($discount) must be of type int|float|null, string given
```

### 2. RuntimeException when Modifying Readonly Properties

**When it occurs:** When trying to modify readonly property after DTO creation.

```php
$dto = new CreateProductDto(/* ... */);

// ❌ Error
$dto->setProperty('price', 200.0);

// Error:
// RuntimeException: Cannot modify readonly property 'price'. 
// Readonly properties can only be set in the constructor.
```

### 3. Error when Direct Access to Readonly Properties

**When it occurs:** When trying direct assignment to readonly property.

```php
$dto = new CreateProductDto(/* ... */);

// ❌ Error
$dto->price = 200.0;

// Error:
// Error: Cannot modify readonly property CreateProductDto::$price
```

## Correct Data Types

### Numeric fields (int|float)
```php
// ✅ Correct
$dto = new CreateProductDto(
    price: 100,        // int
    price: 100.50,     // float
    // ... other parameters
);
```

### Integer fields (int)
```php
// ✅ Correct
$dto = new CreateProductDto(
    quantity: 10,      // int
    quantity: 0,       // int
    // ... other parameters
);
```

### String fields (string)
```php
// ✅ Correct
$dto = new CreateProductDto(
    name: "Product",   // string
    name: "",          // string (empty string)
    // ... other parameters
);
```

### Boolean fields (bool)
```php
// ✅ Correct
$dto = new CreateProductDto(
    is_active: true,   // bool
    is_active: false,  // bool
    // ... other parameters
);
```

### Array fields (array)
```php
// ✅ Correct
$dto = new CreateProductDto(
    tags: ["tag1", "tag2"],  // array
    tags: [],                // array (empty array)
    // ... other parameters
);
```

### Nullable fields
```php
// ✅ Correct
$dto = new CreateProductDto(
    discount: null,    // null
    discount: 15,      // int
    discount: 15.5,    // float
    // ... other parameters
);
```

### Mixed fields (string|int|float)
```php
// ✅ Correct
$dto = new CreateProductDto(
    weight: "1.5",     // string
    weight: 1,         // int
    weight: 1.5,       // float
    // ... other parameters
);
```

## How to Avoid Errors

### 1. Check types before creating DTO
```php
// Type checking
if (!is_numeric($price)) {
    throw new InvalidArgumentException('Price must be numeric');
}

if (!is_int($quantity)) {
    throw new InvalidArgumentException('Quantity must be integer');
}

if (!is_string($name)) {
    throw new InvalidArgumentException('Name must be string');
}

// Creating DTO
$dto = new CreateProductDto(
    price: (float) $price,
    quantity: $quantity,
    name: $name,
    // ... other parameters
);
```

### 2. Use Laravel validation
```php
// In Request class
public function rules(): array
{
    return [
        'price' => 'required|numeric',
        'quantity' => 'required|integer',
        'name' => 'required|string',
        'is_active' => 'required|boolean',
    ];
}

// In controller
public function store(CreateProductRequest $request)
{
    // Laravel already checked types
    $dto = $request->toDto(); // Safe!
}
```

### 3. Use type casting
```php
// Safe type conversion
$dto = new CreateProductDto(
    price: (float) $request->input('price'),
    quantity: (int) $request->input('quantity'),
    name: (string) $request->input('name'),
    is_active: (bool) $request->input('is_active'),
    // ... other parameters
);
```

## Debugging Errors

### 1. Read error messages
TypeError messages contain:
- What type was expected
- What type was passed
- In which parameter the error occurred
- On which line of code

### 2. Use var_dump to check types
```php
// Check types before creating DTO
var_dump([
    'price' => $price,
    'quantity' => $quantity,
    'name' => $name,
    'is_active' => $is_active,
]);
```

### 3. Use gettype() to determine type
```php
echo "Price type: " . gettype($price) . "\n";
echo "Quantity type: " . gettype($quantity) . "\n";
echo "Name type: " . gettype($name) . "\n";
```

## Conclusion

Improved typing in DTOs helps:
- Prevent type errors at development stage
- Make code more reliable and predictable
- Improve code readability and understanding
- Provide better IDE support

Follow this guide to avoid type errors and create higher quality code.