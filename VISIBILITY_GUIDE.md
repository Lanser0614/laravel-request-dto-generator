# Property Visibility Guide for Laravel Request DTO Generator

## Overview

The package supports two visibility levels for readonly properties:
- **Private readonly** (default) - Access only through getter methods
- **Public readonly** - Direct property access

## Differences

### Private Readonly (Default)

```php
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

**Property access:**
```php
$dto = new CreateProductDto(/* ... */);

// ✅ Works - through getter methods
$name = $dto->getName();
$price = $dto->getPrice();

// ❌ Doesn't work - direct access
$name = $dto->name;      // Error: Cannot access private property
$price = $dto->price;    // Error: Cannot access private property
```

**Usage in code:**
```php
// ✅ Correct
$total = $dto->getPrice() * $dto->getQuantity();
echo "Product '{$dto->getName()}' costs {$dto->getPrice()} rub.";

// ❌ Incorrect
$total = $dto->price * $dto->quantity;  // Error
echo "Product '{$dto->name}' costs {$dto->price} rub.";  // Error
```

**Usage in Blade templates:**
```blade
{{-- ❌ Doesn't work --}}
<h1>{{ $dto->name }}</h1>
<p>Price: {{ $dto->price }} rub.</p>

{{-- ✅ Works --}}
<h1>{{ $dto->getName() }}</h1>
<p>Price: {{ $dto->getPrice() }} rub.</p>
```

### Public Readonly

```php
public function __construct(
    public readonly string $name,
    public readonly int|float $price,
    public readonly int $quantity,
) {}
```

**Property access:**
```php
$dto = new CreateProductDto(/* ... */);

// ✅ Works - direct access
$name = $dto->name;
$price = $dto->price;

// ✅ Also works - through getter methods (if generated)
$name = $dto->getName();
$price = $dto->getPrice();
```

**Usage in code:**
```php
// ✅ Works - direct access
$total = $dto->price * $dto->quantity;
echo "Product '{$dto->name}' costs {$dto->price} rub.";

// ✅ Also works - through getter methods
$total = $dto->getPrice() * $dto->getQuantity();
echo "Product '{$dto->getName()}' costs {$dto->getPrice()} rub.";
```

**Usage in Blade templates:**
```blade
{{-- ✅ Works --}}
<h1>{{ $dto->name }}</h1>
<p>Price: {{ $dto->price }} rub.</p>

{{-- ✅ Also works --}}
<h1>{{ $dto->getName() }}</h1>
<p>Price: {{ $dto->getPrice() }} rub.</p>
```

## Configuration

### 1. Publish configuration
```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

### 2. Change setting in config/request-dto-generator.php
```php
// For private readonly (default)
'property_visibility' => 'private',

// For public readonly
'property_visibility' => 'public',
```

### 3. Regenerate DTOs
```bash
php artisan dto:generate --all --force
```

## Performance Comparison

**Private readonly (through getter):** ~0.035s for 1M iterations
**Public readonly (direct access):** ~0.013s for 1M iterations
**Difference:** ~167% (direct access is faster)

## Recommendations

### Use Private Readonly when:

✅ **You need data encapsulation**
- Properties are hidden from external access
- Control over how data is used

✅ **You plan to add logic to getters**
- Validation when getting data
- Data formatting
- Caching calculations

✅ **You need control over data access**
- Logging access to properties
- Auditing data usage

✅ **You follow OOP principles**
- Encapsulation as main principle
- Hiding internal implementation

✅ **DTO may evolve in the future**
- Ability to change internal structure
- Adding new methods without changing API

### Use Public Readonly when:

✅ **You need simple, fast data access**
- DTO used as simple container
- Minimum logic, maximum data

✅ **DTOs are used in templates (Blade, Twig)**
- Direct access in templates
- More readable code in templates

✅ **Performance is critical**
- Direct access is faster than method calls
- Critical for high-load applications

✅ **DTOs are simple data containers**
- No complex logic
- Simple data transfer between layers

✅ **No additional logic needed in getters**
- Data used as is
- No need for validation or formatting

## Usage Examples

### E-commerce Application

**Private Readonly for ProductDto:**
```php
class ProductDto
{
    public function __construct(
        private readonly string $name,
        private readonly int|float $price,
        private readonly int $quantity,
    ) {}

    public function getPrice(): int|float
    {
        // Can add price formatting logic
        return $this->price;
    }

    public function getFormattedPrice(): string
    {
        return number_format($this->price, 2) . ' rub.';
    }
}
```

**Public Readonly for SimpleDataDto:**
```php
class SimpleDataDto
{
    public function __construct(
        public readonly string $name,
        public readonly int|float $value,
        public readonly bool $is_active,
    ) {}
}

// Usage in controller
$data = new SimpleDataDto('test', 100.5, true);
return view('simple', compact('data'));
```

```blade
{{-- In template --}}
<h1>{{ $data->name }}</h1>
<p>Value: {{ $data->value }}</p>
<p>Status: {{ $data->is_active ? 'Active' : 'Inactive' }}</p>
```

## Migration

### From Private to Public
1. Change `'property_visibility' => 'public'` in configuration
2. Regenerate DTOs: `php artisan dto:generate --all --force`
3. Update code, replace `$dto->getProperty()` with `$dto->property`

### From Public to Private
1. Change `'property_visibility' => 'private'` in configuration
2. Regenerate DTOs: `php artisan dto:generate --all --force`
3. Update code, replace `$dto->property` with `$dto->getProperty()`

## Conclusion

The choice between private and public readonly depends on your needs:

- **Private readonly** - for complex DTOs with logic and encapsulation
- **Public readonly** - for simple DTOs with fast data access

The package defaults to private readonly for better encapsulation, but you can easily change this in configuration.