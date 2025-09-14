# Typing Improvements in Laravel Request DTO Generator

## Overview of Changes

The package has been improved for more accurate data type detection based on Laravel validation.

## Key Changes

### 1. Numeric Type Separation

**Before:**
```php
'price' => 'required|numeric', // string|int|float
```

**After:**
```php
'price' => 'required|numeric', // int|float
```

### 2. String Type Preservation

**Before:**
```php
'name' => 'required|string', // string|int|float (incorrect)
```

**After:**
```php
'name' => 'required|string', // string (correct)
```

### 3. Smart Mixed Type Handling

**Before:**
```php
'weight' => 'required|string|numeric', // string|int|float
```

**After:**
```php
'weight' => 'required|string|numeric', // string|int|float (remains as is)
```

## Validation Types and Corresponding PHP Types

| Laravel Validation | PHP Type | Description |
|-------------------|----------|-------------|
| `numeric` | `int\|float` | Numbers only (123, 123.45) |
| `integer` | `int` | Integer numbers only |
| `decimal` | `float` | Float numbers only |
| `string` | `string` | Strings only |
| `string\|numeric` | `string\|int\|float` | Strings or numbers |
| `boolean` | `bool` | Boolean only |
| `accepted` | `bool` | Boolean only |
| `email` | `string` | Strings only |
| `date` | `string` | Strings only |
| `array` | `array` | Arrays only |
| `json` | `array` | Arrays only |

## Usage Examples

### Pure Numeric Types
```php
// Request
'price' => 'required|numeric', // int|float
'discount' => 'nullable|numeric|min:0', // int|float|null

// DTO
private readonly int|float $price,
private readonly int|float|null $discount,

// Usage
$price = $dto->getPrice(); // int|float - can be used directly
$total = $price * 1.2; // Works with int and float
```

### Pure String Types
```php
// Request
'name' => 'required|string|max:255', // string
'email' => 'required|email', // string

// DTO
private readonly string $name,
private readonly string $email,

// Usage
$name = $dto->getName(); // string - can be used directly
$upperName = strtoupper($name);
```

### Mixed Types
```php
// Request
'weight' => 'required|string|numeric', // string|int|float

// DTO
private readonly string|int|float $weight,

// Usage
$weight = $dto->getWeight(); // string|int|float - needs checking
if (is_string($weight)) {
    $weightAsFloat = (float) $weight;
} else {
    $weightAsFloat = (float) $weight;
}
```

## Benefits

1. **Type Accuracy** - `numeric` now means only numbers, not strings
2. **Safety** - prevents type errors at runtime
3. **Performance** - fewer type conversions
4. **Readability** - code becomes more understandable
5. **Compatibility** - works with existing code

## Backward Compatibility

Changes are fully backward compatible:
- Existing DTOs continue to work
- New DTOs get improved typing
- API remains unchanged

## Migration

For existing projects, migration is not required. New DTOs will automatically use improved typing.

To update existing DTOs:
```bash
php artisan dto:generate --all --force
```