# Laravel Request DTO Generator - Namespace Guide

## 🎯 Default Namespace

The package generates DTOs with the default namespace:

```php
namespace App\DTOs;
```

## 🆕 Smart Namespace Mapping

**NEW FEATURE**: The package now automatically maps Request class namespaces to DTO namespaces:

- `App\Http\Requests\TestRequest` → `App\DTOs\TestDto`
- `App\Http\Requests\Api\SentCouponRequest` → `App\DTOs\Api\SentCouponDto`
- `App\Http\Requests\Coupon\CreateCouponRequest` → `App\DTOs\Coupon\CreateCouponDto`

## ⚙️ Configuration

### Default Configuration

```php
// config/request-dto-generator.php
return [
    'dto_namespace' => 'App\\DTOs',
    // ... other options
];
```

### Custom Namespace

You can customize the namespace in the configuration file:

```php
// config/request-dto-generator.php
return [
    'dto_namespace' => 'App\\DataTransferObjects',
    // ... other options
];
```

Or use the command option:

```bash
php artisan dto:generate SentCouponRequest --namespace="App\\DataTransferObjects"
```

## 📁 Directory Structure

### Default Structure

```
app/
├── DTOs/                    # Default DTO directory
│   ├── CreateUserDto.php    # namespace App\DTOs
│   ├── SentCouponDto.php    # namespace App\DTOs
│   └── ItemsDto.php         # namespace App\DTOs
└── Http/
    └── Requests/
        ├── CreateUserRequest.php
        └── Api/
            └── SentCouponRequest.php
```

### Custom Structure

```
app/
├── DataTransferObjects/     # Custom DTO directory
│   ├── CreateUserDto.php    # namespace App\DataTransferObjects
│   ├── SentCouponDto.php    # namespace App\DataTransferObjects
│   └── ItemsDto.php         # namespace App\DataTransferObjects
└── Http/
    └── Requests/
        ├── CreateUserRequest.php
        └── Api/
            └── SentCouponRequest.php
```

## 🔧 Examples

### Example 1: Default Namespace

**Request**: `app/Http/Requests/CreateUserRequest.php`
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
            'email' => 'required|email',
        ];
    }
}
```

**Generated DTO**: `app/DTOs/CreateUserDto.php`
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class CreateUserDto extends BaseDto
{
    public function __construct(
        private readonly string $name,
        private readonly string $email,
    ) {}
}
```

**Usage**:
```php
use App\DTOs\CreateUserDto;

$dto = CreateUserDto::fromArray($data);
```

### Example 2: Request in Subdirectory

**Request**: `app/Http/Requests/Api/SentCouponRequest.php`
```php
<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SentCouponRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'couponCode' => 'required|string|max:50',
            'discountAmount' => 'required|numeric|min:0',
        ];
    }
}
```

**Generated DTO**: `app/DTOs/SentCouponDto.php`
```php
<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class SentCouponDto extends BaseDto
{
    public function __construct(
        private readonly string $couponCode,
        private readonly int|float $discountAmount,
    ) {}
}
```

**Usage**:
```php
use App\DTOs\SentCouponDto;

$dto = SentCouponDto::fromArray($data);
```

### Example 3: Custom Namespace

**Configuration**: `config/request-dto-generator.php`
```php
return [
    'dto_namespace' => 'App\\Api\\DTOs',
    'dto_directory' => app_path('Api/DTOs'),
    // ... other options
];
```

**Generated DTO**: `app/Api/DTOs/SentCouponDto.php`
```php
<?php

namespace App\Api\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;

class SentCouponDto extends BaseDto
{
    public function __construct(
        private readonly string $couponCode,
        private readonly int|float $discountAmount,
    ) {}
}
```

**Usage**:
```php
use App\Api\DTOs\SentCouponDto;

$dto = SentCouponDto::fromArray($data);
```

## 🎯 Best Practices

### 1. Use Consistent Namespace

```php
// Good - consistent with Laravel conventions
'dto_namespace' => 'App\\DTOs',

// Good - descriptive and organized
'dto_namespace' => 'App\\DataTransferObjects',

// Good - feature-based organization
'dto_namespace' => 'App\\Api\\DTOs',
```

### 2. Match Directory Structure

```php
// Configuration
'dto_namespace' => 'App\\DTOs',
'dto_directory' => app_path('DTOs'),

// Generated files
app/DTOs/CreateUserDto.php    // namespace App\DTOs
app/DTOs/SentCouponDto.php    // namespace App\DTOs
```

### 3. Use PSR-4 Autoloading

Make sure your `composer.json` includes the namespace:

```json
{
    "autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
}
```

### 4. Organize by Feature

```php
// API DTOs
'dto_namespace' => 'App\\Api\\DTOs',
'dto_directory' => app_path('Api/DTOs'),

// Admin DTOs
'dto_namespace' => 'App\\Admin\\DTOs',
'dto_directory' => app_path('Admin/DTOs'),

// Common DTOs
'dto_namespace' => 'App\\DTOs',
'dto_directory' => app_path('DTOs'),
```

## 🔄 Migration

### From Custom Namespace to Default

If you want to migrate from a custom namespace to the default:

1. **Update configuration**:
   ```php
   // config/request-dto-generator.php
   'dto_namespace' => 'App\\DTOs',
   'dto_directory' => app_path('DTOs'),
   ```

2. **Regenerate DTOs**:
   ```bash
   php artisan dto:generate --all --force
   ```

3. **Update imports**:
   ```php
   // Before
   use App\DataTransferObjects\CreateUserDto;
   
   // After
   use App\DTOs\CreateUserDto;
   ```

### From Default to Custom Namespace

If you want to migrate to a custom namespace:

1. **Update configuration**:
   ```php
   // config/request-dto-generator.php
   'dto_namespace' => 'App\\DataTransferObjects',
   'dto_directory' => app_path('DataTransferObjects'),
   ```

2. **Regenerate DTOs**:
   ```bash
   php artisan dto:generate --all --force
   ```

3. **Update imports**:
   ```php
   // Before
   use App\DTOs\CreateUserDto;
   
   // After
   use App\DataTransferObjects\CreateUserDto;
   ```

## 🎉 Conclusion

The namespace configuration is flexible and allows you to organize your DTOs according to your project's needs. The default `App\DTOs` namespace follows Laravel conventions and works well for most projects.

**Key points**:
- ✅ **Default namespace**: `App\DTOs`
- ✅ **Customizable**: Change in config or command options
- ✅ **PSR-4 compliant**: Works with Composer autoloading
- ✅ **Consistent**: All DTOs use the same namespace
- ✅ **Organized**: Can be structured by feature or module

**Ready to use**: All generated DTOs will have the correct namespace! 🚀
