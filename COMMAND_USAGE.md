# Laravel Request DTO Generator - Command Usage Guide

## 🚀 Quick Start

The `dto:generate` command can find and generate DTOs from Request classes in various locations and formats.

## 📍 Finding Request Classes

### Basic Usage

```bash
# Generate DTO for a specific Request class
php artisan dto:generate SentCouponRequest

# Generate DTOs for all Request classes
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

### Search Patterns

The command supports multiple search patterns:

| Search Term | Finds |
|-------------|-------|
| `SentCouponRequest` | `App\Http\Requests\Api\SentCouponRequest` |
| `SentCoupon` | `App\Http\Requests\Api\SentCouponRequest` |
| `Api\SentCouponRequest` | `App\Http\Requests\Api\SentCouponRequest` |
| `App\Http\Requests\Api\SentCouponRequest` | Exact match |

### Directory Structure Support

The command searches in:

- **Root directory**: `app/Http/Requests/`
- **Subdirectories**: `app/Http/Requests/Api/`, `app/Http/Requests/Admin/`, etc.
- **Any namespace**: `App\Http\Requests\*`, `App\Requests\*`, etc.

## 🎯 Examples

### Example 1: Basic Request Class

**File**: `app/Http/Requests/CreateUserRequest.php`
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
            'age' => 'required|integer|min:18',
        ];
    }
}
```

**Command**:
```bash
php artisan dto:generate CreateUserRequest
# or
php artisan dto:generate CreateUser
```

**Generated**: `app/DTOs/CreateUserDto.php`

### Example 2: Request Class in Subdirectory

**File**: `app/Http/Requests/Api/SentCouponRequest.php`
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
            'discountType' => 'required|string|in:percentage,fixed',
            'isActive' => 'boolean',
        ];
    }
}
```

**Command**:
```bash
php artisan dto:generate SentCouponRequest
# or
php artisan dto:generate SentCoupon
# or
php artisan dto:generate Api\SentCouponRequest
```

**Generated**: `app/DTOs/SentCouponDto.php`

### Example 3: Complex Request with Nested Structures

**File**: `app/Http/Requests/Admin/CreateOrderRequest.php`
```php
<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class CreateOrderRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'orderNumber' => 'required|string|max:50',
            'customer' => 'required',
            'customer.name' => 'required|string|max:255',
            'customer.email' => 'required|email',
            'items' => 'required|array|min:1',
            'items.*.productId' => 'required|uuid',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
        ];
    }
}
```

**Command**:
```bash
php artisan dto:generate CreateOrderRequest
# or
php artisan dto:generate Admin\CreateOrderRequest
```

**Generated**:
- `app/DTOs/CreateOrderDto.php`
- `app/DTOs/ItemsDto.php`

## ⚙️ Command Options

### Basic Options

```bash
# Generate single DTO
php artisan dto:generate SentCouponRequest

# Generate all DTOs
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

### Advanced Options

```bash
# Custom namespace
php artisan dto:generate SentCouponRequest --namespace="App\\Custom\\DTOs"

# Custom directory
php artisan dto:generate SentCouponRequest --directory="/custom/dto/path"

# Combine options
php artisan dto:generate SentCouponRequest --namespace="App\\Api\\DTOs" --directory="/app/Api/DTOs" --force
```

## 🔍 Troubleshooting

### Class Not Found

If the command can't find your Request class:

1. **Check the file location**:
   ```bash
   # Make sure the file exists
   ls -la app/Http/Requests/
   ls -la app/Http/Requests/Api/
   ```

2. **Check the namespace**:
   ```php
   // Make sure the namespace matches the file location
   namespace App\Http\Requests\Api; // for app/Http/Requests/Api/SentCouponRequest.php
   ```

3. **Check the class name**:
   ```php
   // Make sure the class name matches the file name
   class SentCouponRequest extends FormRequest // for SentCouponRequest.php
   ```

4. **Check the rules method**:
   ```php
   // Make sure the class has a rules() method
   public function rules(): array
   {
       return [
           // your validation rules
       ];
   }
   ```

### Multiple Classes Found

If multiple classes with the same name are found:

```bash
$ php artisan dto:generate CreateUserRequest

Found multiple Request classes with the same name:

1. App\Http\Requests\CreateUserRequest
2. App\Http\Requests\Api\CreateUserRequest
3. App\Admin\Requests\CreateUserRequest

Please select which class to use (1-3): 1
```

**Solution**: Choose the correct class by entering the number.

### Permission Issues

If you get permission errors:

```bash
# Make sure the DTO directory is writable
chmod -R 755 app/DTOs/

# Or use a custom directory
php artisan dto:generate SentCouponRequest --directory="/tmp/dto"
```

## 📋 Best Practices

### 1. Use Descriptive Names

```php
// Good
class CreateUserRequest extends FormRequest
class UpdateProductRequest extends FormRequest
class SentCouponRequest extends FormRequest

// Avoid
class Request1 extends FormRequest
class MyRequest extends FormRequest
```

### 2. Organize by Feature

```
app/Http/Requests/
├── Auth/
│   ├── LoginRequest.php
│   └── RegisterRequest.php
├── Api/
│   ├── CreateUserRequest.php
│   └── UpdateUserRequest.php
└── Admin/
    ├── CreateProductRequest.php
    └── UpdateProductRequest.php
```

### 3. Use Consistent Naming

```php
// Request classes
CreateUserRequest
UpdateUserRequest
DeleteUserRequest

// Generated DTOs
CreateUserDto
UpdateUserDto
DeleteUserDto
```

### 4. Test Your DTOs

```bash
# Generate DTOs
php artisan dto:generate --all

# Test the generated DTOs
php artisan tinker
```

```php
// In tinker
$dto = App\DTOs\CreateUserDto::fromArray([
    'name' => 'John Doe',
    'email' => 'john@example.com',
    'age' => 25
]);

echo $dto->getName(); // John Doe
echo $dto->getEmail(); // john@example.com
```

## 🎉 Conclusion

The `dto:generate` command is designed to be flexible and intuitive. It can find Request classes in various locations and generate appropriate DTOs with proper type hinting and nested structure support.

**Key benefits**:
- ✅ **Smart search** - finds classes by name, with or without "Request" suffix
- ✅ **Subdirectory support** - searches in nested directories
- ✅ **Multiple patterns** - supports various search formats
- ✅ **Batch generation** - generate all DTOs at once
- ✅ **Custom options** - namespace and directory customization
- ✅ **Force overwrite** - update existing DTOs

**Ready to use**: `php artisan dto:generate SentCouponRequest` 🚀
