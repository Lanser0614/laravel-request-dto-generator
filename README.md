# Laravel Request DTO Generator

A Laravel package that automatically generates Data Transfer Objects (DTOs) from your existing Request validation classes. This package helps maintain consistency between your validation logic and business logic by creating DTOs that mirror your Request validation rules.

## Features

- 🚀 **Automatic DTO Generation**: Generate DTOs from existing Request classes
- 🔍 **Smart Type Detection**: Automatically detects PHP types from validation rules
- 🎯 **Flexible Configuration**: Customize namespace, directory, and generation options
- 📝 **Stub-based Templates**: Use customizable templates for consistent DTO generation
- 🛠️ **Artisan Commands**: Easy-to-use command-line interface
- 🔄 **Batch Generation**: Generate DTOs for all Request classes at once
- 🎨 **Base DTO Class**: Rich base class with utility methods
- 🧩 **Trait Support**: Easy integration with existing Request classes
- 🏗️ **Typed Arrays**: Generate separate DTO classes for array items with proper typing
- 🔗 **Nested Structures**: Support for complex nested validation rules
- 📦 **Constructor Property Promotion**: Modern PHP 8+ syntax support

## Installation

1. Install the package via Composer:

```bash
composer require bellissimopizza/laravel-request-dto-generator
```

2. Publish the configuration file:

```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

3. (Optional) Publish stub templates for customization:

```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="stubs"
```

## Advanced Type Handling Examples

### Numeric Fields with Mixed Types

```php
// Request with numeric validation
class CreateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'price' => 'required|numeric',           // int|float
            'weight' => 'required|string|numeric',   // string|int|float
            'quantity' => 'required|integer',        // int
            'discount' => 'nullable|numeric|min:0',  // int|float|null
        ];
    }
}

// Generated DTO
class CreateProductDto extends BaseDto
{
    public function __construct(
        private readonly int|float $price,
        private readonly string|int|float $weight,
        private readonly int $quantity,
        private readonly int|float|null $discount,
    ) {}
}
```

### Safe Type Handling in Business Logic

```php
class ProductService
{
    public function createProduct(CreateProductDto $dto): Product
    {
        // Numeric fields are already int|float
        $price = $dto->getPrice(); // int|float
        $weight = $this->convertToFloat($dto->getWeight()); // string|int|float
        
        // Integer fields are always int
        $quantity = $dto->getQuantity();
        
        // Handle nullable numeric fields
        $discount = $dto->getDiscount(); // int|float|null
        if ($discount !== null) {
            // Already numeric, no conversion needed
        }
        
        return Product::create([
            'price' => $price,
            'weight' => $weight,
            'quantity' => $quantity,
            'discount' => $discount,
        ]);
    }
    
    private function convertToFloat(string|int|float $value): float
    {
        return is_string($value) ? (float) $value : (float) $value;
    }
}
```

## Configuration

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
    'property_visibility' => 'private', // 'private' or 'public'
];
```

## Usage

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
        private readonly string $organizationId,
        private readonly string $orderId,
        private readonly string $sum,
        private readonly string $paymentTypeId,
        private readonly string $code,
        private readonly string $paymentTypeKind,
        private readonly string $oldPaymentType,
        private readonly string $newPaymentType,
        private readonly bool $isProcessedExternally,
        private readonly bool $isPrepay,
    ) {}

    public function getOrganizationId(): string
    {
        return $this->organizationId;
    }

    public function getOrderId(): string
    {
        return $this->orderId;
    }

    // Note: Setters are not generated for readonly properties
    // ... more getters (no setters for readonly properties)
}
```

### Advanced Usage

#### Using the Trait

Add the `GeneratesDto` trait to your Request classes for easy DTO conversion:

```php
<?php

namespace App\Http\Requests;

use BellissimoPizza\RequestDtoGenerator\Traits\GeneratesDto;
use Illuminate\Foundation\Http\FormRequest;

class CreateUserRequest extends FormRequest
{
    use GeneratesDto;

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
        ];
    }
}
```

Then in your controller:

```php
<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateUserRequest;
use App\Services\UserService;

class UserController extends Controller
{
    public function store(CreateUserRequest $request, UserService $userService)
    {
        // Convert Request to DTO
        $userDto = $request->toDto();
        
        // Use DTO in your business logic
        $user = $userService->createUser($userDto);
        
        return response()->json($user);
    }
}
```

#### Custom Namespace and Directory

```bash
# Generate DTO with custom namespace
php artisan dto:generate App\\Http\\Requests\\CreateUserRequest --namespace="App\\DataTransferObjects"

# Generate DTO in custom directory
php artisan dto:generate App\\Http\\Requests\\CreateUserRequest --directory="/path/to/custom/dto/directory"
```

### Base DTO Features

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

// Get/set property values
$name = $dto->getProperty('name');
$dto->setProperty('name', 'Jane Doe');
```

## Command Options

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

The package automatically finds Request classes by name across different namespaces:

- `App\Http\Requests\CreateUserRequest`
- `App\Requests\CreateUserRequest`
- Any other namespace containing the class

If multiple classes with the same name are found, you'll be prompted to choose:

```bash
$ php artisan dto:generate CreateUserRequest

Found multiple Request classes with the same name:

1. App\Http\Requests\CreateUserRequest
2. App\Admin\Requests\CreateUserRequest

Please select which class to use (1-2): 1
```

## Advanced Type Detection

The package intelligently detects PHP types from Laravel validation rules, handling complex scenarios:

### Numeric Fields
Laravel's `numeric` validation accepts integers and floats. The package correctly types these as `int|float`:

```php
// Request validation
'price' => 'required|numeric', // Can accept 123 or 123.45

// Generated DTO
private readonly int|float $price,
```

### Mixed Type Scenarios
When multiple validation rules suggest different types, the package resolves them intelligently:

```php
// Request validation
'weight' => 'required|string|numeric', // String that represents a number

// Generated DTO
private readonly string|int|float $weight,
```

### Boolean Rules
Various boolean validation rules are correctly mapped:

```php
// Request validation
'is_active' => 'required|boolean',     // bool
'accept_terms' => 'required|accepted', // bool
'newsletter' => 'nullable|boolean',    // bool|null
```

### Special Formats
Laravel's format-specific rules are properly typed:

```php
// Request validation
'email' => 'required|email',        // string
'uuid' => 'required|uuid',          // string
'ip' => 'nullable|ip',              // string|null
'date' => 'required|date',          // string
'json' => 'nullable|json',          // array|null
```

## Property Visibility

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

### When to Use Each

**Use Private Readonly when:**
- You need data encapsulation
- You plan to add logic to getter methods
- You want to control data access
- You follow OOP principles

**Use Public Readonly when:**
- You need simple, fast data access
- DTOs are used in templates (Blade, Twig)
- Performance is critical
- DTOs are simple data containers

## Constructor Property Promotion

By default, the package generates DTOs using PHP 8's constructor property promotion feature. This creates a clean, concise syntax where properties are declared as constructor parameters with configurable visibility.

### Benefits of Constructor Property Promotion

- **Cleaner Code**: Properties are declared and initialized in one place
- **Less Boilerplate**: No need for separate property declarations and constructor assignments
- **Immutability**: Properties are automatically readonly and private
- **Modern PHP**: Uses the latest PHP 8+ features for better performance and readability

### Configuration

You can control constructor property promotion in the configuration:

```php
// config/request-dto-generator.php
'constructor_property_promotion' => true, // Enable constructor property promotion (default)
```

When `constructor_property_promotion` is enabled:
- Properties are declared as `private readonly` constructor parameters
- No separate property declarations are generated
- Constructor body is empty `{}`
- Only getters are generated (no setters)

## Readonly Properties

By default, the package generates DTOs with readonly properties for immutability. This ensures that once a DTO is created, its values cannot be modified, which is ideal for data transfer objects.

### Benefits of Readonly Properties

- **Immutability**: Once created, DTO values cannot be accidentally modified
- **Thread Safety**: Readonly objects are inherently thread-safe
- **Clear Intent**: Makes it obvious that the object represents immutable data
- **Better Performance**: PHP can optimize readonly properties

### Configuration

You can control readonly behavior in the configuration:

```php
// config/request-dto-generator.php
'readonly_properties' => true, // Enable readonly properties (default)
```

When `readonly_properties` is enabled:
- Properties are declared as `public readonly` with comma formatting (except the last property)
- Only getters are generated (no setters)
- Properties can only be set in the constructor
- The `setProperty()` method will throw an exception for readonly properties

### Working with Constructor Property Promotion DTOs

```php
// Create DTO (values can only be set in constructor)
$dto = new CreateUserDto(
    organizationId: 'org-123',
    orderId: 'order-456',
    sum: '100.00',
    paymentTypeId: 'pay-789',
    code: 'CODE123',
    paymentTypeKind: 'credit_card',
    oldPaymentType: 'cash',
    newPaymentType: 'card',
    isProcessedExternally: true,
    isPrepay: false
);

// Read values through getters
$organizationId = $dto->getOrganizationId();
$orderId = $dto->getOrderId();

// Properties are private, so direct access won't work
// $dto->organizationId; // This will cause an error!

// This will throw an exception for readonly properties
// $dto->setProperty('organizationId', 'new-org'); // Runtime exception!

// Check if property is readonly
$isReadonly = $dto->isPropertyReadonly('organizationId'); // true
```

## Type Detection

The package automatically detects PHP types from Laravel validation rules:

| Validation Rule | Detected Type |
|----------------|---------------|
| `string` | `string` |
| `integer`, `int` | `int` |
| `numeric`, `decimal` | `float` |
| `boolean`, `bool` | `bool` |
| `array` | `array` |
| `nullable` | Adds `?` to type |
| `array:*` | `Type[]` (array of specific type) |

## Customization

### Custom Stubs

You can customize the generated DTO structure by publishing and modifying the stub files:

```bash
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="stubs"
```

This will copy the stub files to `stubs/request-dto-generator/` where you can modify them.

### Custom Base Class

You can create your own base DTO class and configure it in the config file:

```php
// config/request-dto-generator.php
'dto_base_class' => 'App\\DTOs\\BaseDto',
```

## Examples

### Typed Arrays Example

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email',
            'items' => 'required|array',
            'items.*.productId' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'combos' => 'nullable|array',
            'combos.*.id' => 'required|uuid',
            'combos.*.amount' => 'required|integer|min:1',
            'payments' => 'nullable|array',
            'payments.*.paymentTypeId' => 'required|uuid',
            'payments.*.amount' => 'required|numeric|min:0',
        ];
    }
}
```

Generated DTOs:

**ItemDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class ItemDto extends BaseDto
{
    public function __construct(
        private readonly string $productId,
        private readonly int $quantity,
        private readonly int|float $price
    ) {}
}
```

**ComboDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class ComboDto extends BaseDto
{
    public function __construct(
        private readonly string $id,
        private readonly int $amount
    ) {}
}
```

**PaymentDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class PaymentDto extends BaseDto
{
    public function __construct(
        private readonly string $paymentTypeId,
        private readonly int|float $amount
    ) {}
}
```

**CreateOrderDto.php:**
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\ItemDto;
use App\DTOs\ComboDto;
use App\DTOs\PaymentDto;

class CreateOrderDto extends BaseDto
{
    public function __construct(
        private readonly string $customerName,
        private readonly string $customerEmail,
        private readonly ?ItemDto[] $items = null,
        private readonly ?ComboDto[] $combos = null,
        private readonly ?PaymentDto[] $payments = null
    ) {}
}
```

### Complex Request with Nested Data

```php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'price' => 'required|numeric|min:0',
            'category_id' => 'required|integer|exists:categories,id',
            'images' => 'array|max:5',
            'images.*' => 'url',
            'specifications' => 'nullable|array',
            'specifications.*.name' => 'required|string',
            'specifications.*.value' => 'required|string',
            'is_featured' => 'boolean',
            'tags' => 'array',
            'tags.*' => 'string|max:50',
        ];
    }
}
```

Generated DTO:

```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class UpdateProductDto extends BaseDto
{
    public function __construct(
        private readonly string $name,
        private readonly ?string $description,
        private readonly float $price,
        private readonly int $category_id,
        private readonly array $images,
        private readonly ?array $specifications,
        private readonly bool $is_featured,
        private readonly array $tags,
    ) {}

    // Getters (no setters for readonly properties)...
}
```

## Documentation

### 📚 Additional Guides

- **[Type Errors Guide](TYPE_ERRORS_GUIDE.md)** - Complete guide to type errors and how to avoid them
- **[Visibility Guide](VISIBILITY_GUIDE.md)** - Detailed comparison of public vs private readonly properties
- **[Typing Improvements](TYPING_IMPROVEMENTS.md)** - Overview of advanced type detection features
- **[Nested Structures Guide](NESTED_STRUCTURES_GUIDE.md)** - Complete guide to nested structures and complex data handling

### 📁 Examples

- **[Advanced Validation Examples](examples/advanced-validation-examples.php)** - Complex validation scenarios
- **[Generated DTO Examples](examples/generated-dto-examples.php)** - Examples of generated DTOs with improved typing
- **[Type Handling Demo](examples/type-handling-demo.php)** - Complete demonstration of type handling
- **[Visibility Demo](examples/visibility-demo-simple.php)** - Simple demonstration of property visibility differences
- **[Error Examples](examples/error-examples.php)** - Examples of type errors you might encounter
- **[Nested Structures Example](examples/nested-structures-example.php)** - Complex nested structures example
- **[Usage Examples](examples/usage-examples.md)** - Comprehensive usage examples

### 🔧 Configuration Options

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

### 🚀 Quick Start Examples

#### Basic Usage
```bash
# Generate DTO for a specific Request class
php artisan dto:generate CreateUserRequest

# Generate DTOs for all Request classes
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

#### Advanced Usage
```bash
# Custom namespace
php artisan dto:generate CreateUserRequest --namespace="App\\DataTransferObjects"

# Custom directory
php artisan dto:generate CreateUserRequest --directory="/custom/dto/path"
```

### 🎯 Key Features

#### Smart Type Detection
- `numeric` → `int|float` (numbers only)
- `string` → `string` (strings only)
- `string|numeric` → `string|int|float` (mixed types)
- `boolean` → `bool`
- `array` → `array`
- `nullable` → adds `|null` to type

#### Property Visibility Options
- **Private Readonly** (default): Access only through getter methods
- **Public Readonly**: Direct property access + getter methods

#### Constructor Property Promotion
```php
// Generated with constructor property promotion
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

#### Typed Arrays Support
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

#### Nested Structures Support
```php
// Request with nested validation rules
'order.deliveryPoint.coordinates.latitude' => 'required|numeric',
'order.deliveryPoint.coordinates.longitude' => 'required|numeric',
'order.items.*.productId' => 'required|uuid',
'order.items.*.quantity' => 'required|integer',

// Generated DTO with nested structures
public function __construct(
    private readonly array $order, // Nested structure
    private readonly array $items, // Array of nested objects
) {}
```

## Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

If you find this package helpful, please consider giving it a ⭐ on GitHub!
# laravel-request-dto-generator
