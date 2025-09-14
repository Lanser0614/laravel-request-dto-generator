# Laravel Request DTO Generator - Compatibility Guide

## 🎯 Supported Versions

### Laravel Framework
- ✅ **Laravel 9.x** - Full support
- ✅ **Laravel 10.x** - Full support  
- ✅ **Laravel 11.x** - Full support

### PHP Version
- ✅ **PHP 8.0** - Minimum required version
- ✅ **PHP 8.1** - Recommended version
- ✅ **PHP 8.2** - Full support
- ✅ **PHP 8.3** - Full support

## 🔧 Compatibility Features

### Laravel 9.x Support
The package is fully compatible with Laravel 9.x and includes:

- ✅ **Illuminate\Support** - Service provider registration
- ✅ **Illuminate\Console** - Artisan command functionality
- ✅ **Illuminate\Filesystem** - File system operations
- ✅ **Form Request validation** - Request class integration
- ✅ **Configuration publishing** - Config file publishing
- ✅ **Stub publishing** - Template customization

### PHP 8.0+ Features Used
The package leverages modern PHP features:

- ✅ **Union Types** (`int|float`, `string|null`) - Type hinting
- ✅ **Constructor Property Promotion** - Clean DTO syntax
- ✅ **Readonly Properties** - Immutable DTOs (PHP 8.1+)
- ✅ **str_ends_with()** - String utilities (PHP 8.0+)
- ✅ **Named Arguments** - Function calls (PHP 8.0+)

## 🧪 Testing Compatibility

### Run Compatibility Tests

```bash
# Test Laravel 9 compatibility
php examples/test-laravel9-compatibility.php

# Test basic functionality
php examples/simple-final-example.php

# Test Artisan commands
php examples/test-artisan-direct.php
```

### What the Tests Check

1. **PHP Version Compatibility**
   - PHP 8.0+ requirement
   - Modern PHP features availability

2. **Laravel Components**
   - Illuminate\Support availability
   - Illuminate\Console availability
   - Illuminate\Filesystem availability

3. **Package Functionality**
   - Request classes discovery
   - DTO generation
   - Multiple DTOs generation
   - Generated DTOs functionality

4. **PHP 8.0+ Features**
   - str_ends_with function
   - Union types support
   - Readonly properties support

## 📋 Installation by Laravel Version

### Laravel 9.x
```bash
composer require bellissimopizza/laravel-request-dto-generator
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

### Laravel 10.x
```bash
composer require bellissimopizza/laravel-request-dto-generator
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

### Laravel 11.x
```bash
composer require bellissimopizza/laravel-request-dto-generator
php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
```

## ⚠️ Known Limitations

### Laravel 9.x Specific
- Some advanced validation rules may not be fully supported
- Custom validation rules might need additional configuration
- Performance may vary with large validation rule sets

### PHP 8.0 Specific
- Readonly properties require PHP 8.1+ (fallback to regular properties)
- Some modern PHP features may not be available

## 🔄 Migration Guide

### From Laravel 8.x to 9.x
If you're upgrading from Laravel 8.x:

1. **Update PHP** to 8.0 or higher
2. **Update Laravel** to 9.x
3. **Install the package**:
   ```bash
   composer require bellissimopizza/laravel-request-dto-generator
   ```
4. **Publish configuration**:
   ```bash
   php artisan vendor:publish --provider="BellissimoPizza\RequestDtoGenerator\RequestDtoGeneratorServiceProvider" --tag="config"
   ```

### From Laravel 9.x to 10.x/11.x
The package is fully compatible across Laravel versions:

1. **Update Laravel** to 10.x or 11.x
2. **No package changes needed** - full compatibility
3. **Test functionality** with compatibility tests

## 🐛 Troubleshooting

### Common Issues

#### PHP Version Issues
```bash
# Check PHP version
php --version

# Should be 8.0 or higher
```

#### Laravel Version Issues
```bash
# Check Laravel version
php artisan --version

# Should be 9.x, 10.x, or 11.x
```

#### Package Installation Issues
```bash
# Clear composer cache
composer clear-cache

# Reinstall package
composer remove bellissimopizza/laravel-request-dto-generator
composer require bellissimopizza/laravel-request-dto-generator
```

### Getting Help

If you encounter compatibility issues:

1. **Run compatibility tests** to identify the problem
2. **Check PHP and Laravel versions** meet requirements
3. **Open an issue** with your environment details
4. **Provide test results** from compatibility tests

## 📊 Compatibility Matrix

| Laravel Version | PHP Version | Package Support | Status |
|----------------|-------------|-----------------|---------|
| 9.x | 8.0+ | ✅ Full | Supported |
| 9.x | 8.1+ | ✅ Full | Recommended |
| 10.x | 8.0+ | ✅ Full | Supported |
| 10.x | 8.1+ | ✅ Full | Recommended |
| 11.x | 8.0+ | ✅ Full | Supported |
| 11.x | 8.1+ | ✅ Full | Recommended |

## 🎉 Conclusion

The Laravel Request DTO Generator is fully compatible with Laravel 9.x, 10.x, and 11.x, providing a consistent experience across all supported Laravel versions. The package leverages modern PHP 8.0+ features while maintaining backward compatibility where possible.

For the best experience, we recommend using PHP 8.1+ with Laravel 9.x or higher.
