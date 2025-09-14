# Laravel Request DTO Generator

> ⚠️ **Disclaimer**: This package is not perfect and is still under active development. We welcome new suggestions, improvements, and contributions from the community. If you have ideas for enhancements or encounter issues, please feel free to open an issue or submit a pull request.

A Laravel package that automatically generates Data Transfer Objects (DTOs) from your existing Request validation classes using advanced JSON Schema generation. This package helps maintain consistency between your validation logic and business logic by creating DTOs that mirror your Request validation rules with proper type hinting and nested structure support.

## ✨ Features

- 🚀 **Automatic DTO Generation**: Generate DTOs from existing Request classes
- 🔍 **Smart Type Detection**: Automatically detects PHP types from validation rules
- 🎯 **Flexible Configuration**: Customize namespace, directory, and generation options
- 🛠️ **Artisan Commands**: Easy-to-use command-line interface
- 🔄 **Batch Generation**: Generate DTOs for all Request classes at once
- 🎨 **Base DTO Class**: Rich base class with utility methods
- 🏗️ **Typed Arrays**: Generate separate DTO classes for array items with proper typing
- 🔗 **Nested Structures**: Support for complex nested validation rules
- 📦 **Constructor Property Promotion**: Modern PHP 8+ syntax support
- 🔒 **Readonly Properties**: Immutable DTOs for data integrity
- 🎭 **ValidationSchemaGenerator**: Advanced JSON Schema generation from Laravel validation rules
- 🔄 **Laravel 9+ Support**: Compatible with Laravel 9.x, 10.x, and 11.x

## 🚀 Installation

### Requirements

- PHP 8.0 or higher
- Laravel 9.x, 10.x, or 11.x

### Install the package via Composer:

```bash
composer require bellissimopizza/laravel-request-dto-generator
```

### Publish the configuration file:

```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

### (Optional) Publish stub templates for customization:

```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="stubs"
```

## ⚙️ Configuration

The package configuration is located in `config/request-dto-generator.php`:

```php
return [
    'dto_namespace' => 'App\\DTOs',
    'dto_directory' => app_path('DTOs'),
    'request_directory' => app_path('Http/Requests'),
    'dto_base_class' => 'BellissimoPizza\\RequestDtoGenerator\\BaseDto',
    'auto_generate_properties' => true,
    'include_validation_rules' => true,
    'generate_constructor' => true,
    'generate_accessors' => true,
    'readonly_properties' => true,
    'constructor_property_promotion' => true,
    'property_visibility' => 'private',
    'generate_separate_dtos_for_arrays' => true,
];
```

## 📖 Usage

### Basic Usage

#### 1. Create a Request Class

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'age' => 'required|integer|min:18|max:120',
            'is_active' => 'boolean',
            'profile' => 'nullable|array',
            'profile.bio' => 'nullable|string|max:500',
            'profile.avatar' => 'nullable|url',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ];
    }
}
```

#### 2. Generate DTO

```bash
# Generate DTO for a specific Request class by name
php artisan dto:generate CreateUserRequest

# Generate DTOs for all Request classes
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

#### 3. Generated DTO

The package will generate a DTO like this:

```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class CreateUserDto extends BaseDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
        private readonly int $age,
        private readonly bool $isActive,
        private readonly ?array $profile = null,
        private readonly array $tags = []
    ) {}

    public function getName(): string
    {
        return $this->name;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getAge(): int
    {
        return $this->age;
    }

    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    public function getProfile(): ?array
    {
        return $this->profile;
    }

    public function getTags(): array
    {
        return $this->tags;
    }
}
```

### Advanced Usage with Typed Arrays

#### Complex Request with Nested Structures

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'orderNumber' => 'required|string|max:50',
            'orderDate' => 'required|date',
            'totalAmount' => 'required|numeric',
            'isPaid' => 'boolean',
            
            // Customer information
            'customer' => 'required',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email',
            'customer.phone' => 'nullable|string|max:20',
            
            // Customer address
            'customer.address' => 'required',
            'customer.address.street' => 'required|string|max:100',
            'customer.address.city' => 'required|string|max:50',
            'customer.address.zipCode' => 'required|string|max:10',
            'customer.address.country' => 'required|string|max:50',
            
            // Order items (typed array)
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|uuid',
            'items.*.productName' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unitPrice' => 'required|numeric|min:0',
            'items.*.totalPrice' => 'required|numeric|min:0',
            
            // Item modifiers (nested typed array)
            'items.*.modifiers' => 'nullable|array',
            'items.*.modifiers.*.modifierId' => 'required|uuid',
            'items.*.modifiers.*.name' => 'required|string|max:100',
            'items.*.modifiers.*.price' => 'required|numeric',
            
            // Payment information (typed array)
            'payments' => 'nullable|array',
            'payments.*.paymentId' => 'required|uuid',
            'payments.*.amount' => 'required|numeric|min:0',
            'payments.*.method' => 'required|string|in:cash,card,online',
            'payments.*.transactionId' => 'nullable|string|max:100',
            
            // Additional fields
            'notes' => 'nullable|string|max:1000',
        ];
    }
}
```

#### Generated DTOs

The package will generate multiple DTO classes:

**CreateOrderDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class CreateOrderDto extends BaseDto
{
    public function __construct(
        private readonly string $orderNumber,
        private readonly string $orderDate,
        private readonly int|float $totalAmount,
        private readonly bool $isPaid,
        private readonly array $customer,
        private readonly array $items,
        private readonly ?array $payments = null,
        private readonly ?string $notes = null
    ) {}
}
```

**ItemsDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class ItemsDto extends BaseDto
{
    public function __construct(
        private readonly string $productId,
        private readonly string $productName,
        private readonly int $quantity,
        private readonly int|float $unitPrice,
        private readonly int|float $totalPrice,
        private readonly ?array $modifiers = null
    ) {}
}
```

**ModifiersDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class ModifiersDto extends BaseDto
{
    public function __construct(
        private readonly string $modifierId,
        private readonly string $name,
        private readonly int|float $price
    ) {}
}
```

**PaymentsDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class PaymentsDto extends BaseDto
{
    public function __construct(
        private readonly string $paymentId,
        private readonly int|float $amount,
        private readonly string $method,
        private readonly ?string $transactionId = null
    ) {}
}
```

### Using Generated DTOs

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateOrderRequest;
use App\DTOs\CreateOrderDto;
use App\DTOs\ItemsDto;
use App\DTOs\ModifiersDto;
use App\DTOs\PaymentsDto;

class OrderController extends Controller
{
    public function store(CreateOrderRequest $request)
    {
        // Convert Request to DTO
        $orderDto = CreateOrderDto::fromArray($request->validated());
        
        // Use DTO in your business logic
        $order = $this->orderService->createOrder($orderDto);
        
        return response()->json($order);
    }
}
```

## 🎯 Command Options

### `dto:generate` Command

```bash
php artisan dto:generate {request?} {options}
```

**Arguments:**
- `request` - The Request class name to generate DTO from (e.g., CreateUserRequest)

**Options:**
- `--all` - Generate DTOs for all Request classes
- `--force` - Overwrite existing DTO files
- `--namespace=` - Custom namespace for generated DTOs
- `--directory=` - Custom directory for generated DTOs

**Examples:**

```bash
# Generate single DTO by class name
php artisan dto:generate CreateUserRequest

# Generate all DTOs
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force

# Custom namespace
php artisan dto:generate CreateUserRequest --namespace="App\\DataTransferObjects"

# Custom directory
php artisan dto:generate CreateUserRequest --directory="/custom/path"
```

### Smart Class Discovery

The package automatically finds Request classes by name across different namespaces and subdirectories:

- `App\Http\Requests\CreateUserRequest` (root directory)
- `App\Http\Requests\Api\CreateUserRequest` (subdirectory)
- `App\Http\Requests\Admin\CreateUserRequest` (subdirectory)
- `App\Requests\CreateUserRequest` (different namespace)
- Any other namespace containing the class

**Search patterns supported:**
- `SentCouponRequest` - finds `App\Http\Requests\Api\SentCouponRequest`
- `SentCoupon` - finds `App\Http\Requests\Api\SentCouponRequest`
- `Api\SentCouponRequest` - finds `App\Http\Requests\Api\SentCouponRequest`
- `App\Http\Requests\Api\SentCouponRequest` - exact match

If multiple classes with the same name are found, you'll be prompted to choose:

### Smart Namespace Mapping

**NEW FEATURE**: The package now automatically maps Request class namespaces to DTO namespaces:

- `App\Http\Requests\TestRequest` → `App\DTOs\TestDto`
- `App\Http\Requests\Api\SentCouponRequest` → `App\DTOs\Api\SentCouponDto`
- `App\Http\Requests\Coupon\CreateCouponRequest` → `App\DTOs\Coupon\CreateCouponDto`

This means DTOs are organized in the same directory structure as your Request classes, making your codebase more organized and maintainable.

```bash
$ php artisan dto:generate CreateUserRequest

Found multiple Request classes with the same name:

1. App\Http\Requests\CreateUserRequest
2. App\Http\Requests\Api\CreateUserRequest
3. App\Admin\Requests\CreateUserRequest

Please select which class to use (1-3): 1
```

## 🔍 Smart Type Detection

The package intelligently detects PHP types from Laravel validation rules:

| Validation Rule | Detected Type |
|----------------|---------------|
| `string` | `string` |
| `integer`, `int` | `int` |
| `numeric` | `int\|float` |
| `boolean`, `bool` | `bool` |
| `array` | `array` |
| `nullable` | Adds `?` to type |
| `array:*` | `Type[]` (array of specific type) |

### Advanced Type Detection Examples

```php
// Request validation
'price' => 'required|numeric',           // int|float
'weight' => 'required|string|numeric',   // string|int|float
'quantity' => 'required|integer',        // int
'discount' => 'nullable|numeric|min:0',  // int|float|null
'is_active' => 'required|boolean',       // bool
'email' => 'required|email',             // string
'uuid' => 'required|uuid',               // string
'date' => 'required|date',               // string
'json' => 'nullable|json',               // array|null
```

## 🏗️ Base DTO Features

The generated DTOs extend `BaseDto` which provides useful utility methods:

```php
$dto = new CreateUserDto('John Doe', 'john@example.com', 25, true);

// Convert to array
$array = $dto->toArray();

// Convert to JSON
$json = $dto->toJson();

// Create from array
$dto = CreateUserDto::fromArray($data);

// Create from JSON
$dto = CreateUserDto::fromJson($jsonString);

// Get all property names
$properties = $dto->getPropertyNames();

// Check if property exists
$hasName = $dto->hasProperty('name');

// Get property values
$name = $dto->getProperty('name');
```

## 🔒 Property Visibility

The package supports two visibility levels for readonly properties:

### Private Readonly (Default)
```php
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

**Access:** Only through getter methods
```php
$dto = new CreateProductDto(/* ... */);
$name = $dto->getName(); // ✅ Works
$name = $dto->name;      // ❌ Error: Cannot access private property
```

### Public Readonly
```php
public function __construct(
    public readonly string $name,
    public readonly int|float $price,
    public readonly int $quantity,
) {}
```

**Access:** Direct property access
```php
$dto = new CreateProductDto(/* ... */);
$name = $dto->name;      // ✅ Works
$name = $dto->getName(); // ✅ Also works (if getters are generated)
```

### Configuration

Set the visibility in `config/request-dto-generator.php`:

```php
'property_visibility' => 'private', // Default: private readonly
'property_visibility' => 'public',  // Alternative: public readonly
```

## 🎭 ValidationSchemaGenerator

The package uses an advanced `ValidationSchemaGenerator` that converts Laravel validation rules into JSON Schema format, enabling:

- **Complex nested structures** support
- **Typed arrays** with separate DTO classes
- **Proper type hinting** for all data types
- **Recursive DTO generation** for deeply nested objects
- **Array item DTOs** for structured data

### How it works:

1. **Parse Laravel Rules**: Converts validation rules to JSON Schema
2. **Generate DTOs**: Creates PHP classes from JSON Schema
3. **Type Detection**: Maps validation rules to PHP types
4. **Nested Structures**: Handles complex object hierarchies
5. **Array Processing**: Creates separate DTOs for array items

## ⚠️ Known Limitations

This package is still under development and has some limitations:

- **Complex validation rules** may not be fully supported
- **Custom validation rules** might not be recognized
- **Some edge cases** in nested structures may not work perfectly
- **Performance** could be improved for large validation rule sets
- **IDE support** for generated DTOs could be enhanced

**We're working on improving these areas and welcome your feedback!**

## 🧪 Testing

The package includes comprehensive test examples:

```bash
# Run the simple final example
php examples/simple-final-example.php

# Test Artisan command functionality
php examples/test-artisan-direct.php

# Test Laravel 9 compatibility
php examples/test-laravel9-compatibility.php

# Test Request class discovery in subdirectories
php examples/test-search-logic.php
```

## 📚 Additional Documentation

- **[Command Usage Guide](COMMAND_USAGE.md)** - Complete guide to using the dto:generate command
- **[Namespace Guide](NAMESPACE_GUIDE.md)** - Complete guide to DTO namespaces and organization
- **[Compatibility Guide](COMPATIBILITY.md)** - Laravel 9.x, 10.x, 11.x compatibility information
- **[Artisan Usage Guide](ARTISAN_USAGE.md)** - Complete Artisan command usage guide
- **[Type Errors Guide](TYPE_ERRORS_GUIDE.md)** - Complete guide to type errors and how to avoid them
- **[Visibility Guide](VISIBILITY_GUIDE.md)** - Detailed comparison of public vs private readonly properties
- **[Typing Improvements](TYPING_IMPROVEMENTS.md)** - Overview of advanced type detection features
- **[Nested Structures Guide](NESTED_STRUCTURES_GUIDE.md)** - Complete guide to nested structures and complex data handling

## 🔧 Configuration Options

| Option | Default | Description |
|--------|---------|-------------|
| `dto_namespace` | `App\\DTOs` | Namespace for generated DTOs |
| `dto_directory` | `app_path('DTOs')` | Directory for generated DTOs |
| `request_directory` | `app_path('Http/Requests')` | Directory where Request files are located |
| `dto_base_class` | `BellissimoPizza\\RequestDtoGenerator\\BaseDto` | Base class for all DTOs |
| `auto_generate_properties` | `true` | Auto-generate properties from validation rules |
| `include_validation_rules` | `true` | Include validation rules as comments |
| `generate_constructor` | `true` | Generate constructor with all properties |
| `generate_accessors` | `true` | Generate getter and setter methods |
| `readonly_properties` | `true` | Make properties readonly for immutability |
| `constructor_property_promotion` | `true` | Use PHP 8+ constructor property promotion |
| `property_visibility` | `'private'` | Property visibility: `'private'` or `'public'` |
| `generate_separate_dtos_for_arrays` | `true` | Generate separate DTO classes for array items |

## 🚀 Quick Start Examples

### Basic Usage
```bash
# Generate DTO for a specific Request class
php artisan dto:generate CreateUserRequest

# Generate DTOs for all Request classes
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

### Advanced Usage
```bash
# Custom namespace
php artisan dto:generate CreateUserRequest --namespace="App\\DataTransferObjects"

# Custom directory
php artisan dto:generate CreateUserRequest --directory="/custom/dto/path"
```

## 🎯 Key Features

### Smart Type Detection
- `numeric` → `int|float` (numbers only)
- `string` → `string` (strings only)
- `string|numeric` → `string|int|float` (mixed types)
- `boolean` → `bool`
- `array` → `array`
- `nullable` → adds `|null` to type

### Constructor Property Promotion
```php
// Generated with constructor property promotion
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

### Typed Arrays Support
```php
// Request with array validation rules
'items' => 'required|array',
'items.*.productId' => 'required|uuid',
'items.*.quantity' => 'required|integer',
'items.*.price' => 'required|numeric',

// Generated separate DTO classes
class ItemDto extends BaseDto {
    public function __construct(
        private readonly string $productId,
        private readonly int $quantity,
        private readonly int|float $price
    ) {}
}

class CreateOrderDto extends BaseDto {
    public function __construct(
        private readonly ?ItemDto[] $items = null // Typed array!
    ) {}
}
```

### Nested Structures Support
```php
// Request with nested validation rules
'customer.address.street' => 'required|string',
'customer.address.city' => 'required|string',
'items.*.modifiers.*.name' => 'required|string',

// Generated DTO with nested structures
public function __construct(
    private readonly array $customer, // Nested structure
    private readonly array $items,    // Array of nested objects
) {}
```

## 🤝 Contributing

We actively welcome contributions and suggestions! This package is not perfect and we're always looking to improve it.

### How to Contribute

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### What We're Looking For

- 🐛 **Bug fixes** - Help us make the package more stable
- ✨ **New features** - Suggest and implement new functionality
- 📚 **Documentation** - Improve guides and examples
- 🧪 **Tests** - Add more test coverage
- 💡 **Ideas** - Share your thoughts on improvements
- 🔧 **Performance** - Optimize existing code

### Areas for Improvement

- Better error handling and validation
- More validation rule types support
- Enhanced nested structure handling
- Performance optimizations
- Additional configuration options
- Better IDE support and autocompletion

**Your feedback and contributions are highly appreciated!** 🙏

## 📄 License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## ⭐ Support

If you find this package helpful, please consider giving it a ⭐ on GitHub!

---

**Laravel Request DTO Generator** - Automatically generate type-safe DTOs from your Laravel Request validation rules with advanced nested structure support and modern PHP 8+ features.