# Laravel Request DTO Generator - Documentation Index

## 📚 Main Documentation

- **[README.md](README.md)** - Main package documentation with installation and basic usage
- **[TYPE_ERRORS_GUIDE.md](TYPE_ERRORS_GUIDE.md)** - Complete guide to type errors and how to avoid them
- **[VISIBILITY_GUIDE.md](VISIBILITY_GUIDE.md)** - Detailed comparison of public vs private readonly properties
- **[TYPING_IMPROVEMENTS.md](TYPING_IMPROVEMENTS.md)** - Overview of advanced type detection features

## 📁 Examples Directory

### PHP Examples
- **[advanced-validation-examples.php](examples/advanced-validation-examples.php)** - Complex validation scenarios with various Laravel validation rules
- **[generated-dto-examples.php](examples/generated-dto-examples.php)** - Examples of generated DTOs with improved typing
- **[type-handling-demo.php](examples/type-handling-demo.php)** - Complete demonstration of type handling features
- **[visibility-demo-simple.php](examples/visibility-demo-simple.php)** - Simple demonstration of property visibility differences
- **[error-examples.php](examples/error-examples.php)** - Examples of type errors you might encounter
- **[readonly-visibility-demo.php](examples/readonly-visibility-demo.php)** - Detailed comparison of readonly property visibility
- **[visibility-comparison.php](examples/visibility-comparison.php)** - Comprehensive visibility comparison with performance tests

### Markdown Examples
- **[usage-examples.md](examples/usage-examples.md)** - Comprehensive usage examples and best practices

## 🎯 Quick Navigation

### Getting Started
1. [Installation](README.md#installation) - How to install the package
2. [Configuration](README.md#configuration) - Package configuration options
3. [Basic Usage](README.md#usage) - Simple examples to get started

### Advanced Features
1. [Smart Type Detection](README.md#advanced-type-detection) - How the package detects PHP types
2. [Property Visibility](README.md#property-visibility) - Public vs Private readonly properties
3. [Constructor Property Promotion](README.md#constructor-property-promotion) - PHP 8+ features

### Troubleshooting
1. [Type Errors Guide](TYPE_ERRORS_GUIDE.md) - Common type errors and solutions
2. [Error Examples](examples/error-examples.php) - Run these to see actual error messages

### Best Practices
1. [Usage Examples](examples/usage-examples.md) - Comprehensive examples and patterns
2. [Advanced Validation](examples/advanced-validation-examples.php) - Complex validation scenarios
3. [Visibility Guide](VISIBILITY_GUIDE.md) - When to use public vs private readonly

## 🔧 Configuration Reference

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

## 🚀 Command Reference

### Basic Commands
```bash
# Generate DTO for a specific Request class
php artisan dto:generate CreateUserRequest

# Generate DTOs for all Request classes
php artisan dto:generate --all

# Force overwrite existing DTOs
php artisan dto:generate --all --force
```

### Advanced Commands
```bash
# Custom namespace
php artisan dto:generate CreateUserRequest --namespace="App\\DataTransferObjects"

# Custom directory
php artisan dto:generate CreateUserRequest --directory="/custom/dto/path"
```

## 📊 Type Mapping Reference

| Laravel Validation | PHP Type | Description |
|-------------------|----------|-------------|
| `numeric` | `int\|float` | Numbers only (123, 123.45) |
| `integer` | `int` | Integer numbers only |
| `decimal` | `float` | Float numbers only |
| `string` | `string` | Strings only |
| `string\|numeric` | `string\|int\|float` | Mixed types |
| `boolean` | `bool` | Boolean values |
| `accepted` | `bool` | Boolean values |
| `email` | `string` | Email strings |
| `url` | `string` | URL strings |
| `uuid` | `string` | UUID strings |
| `ip` | `string` | IP address strings |
| `date` | `string` | Date strings |
| `array` | `array` | Arrays |
| `json` | `array` | JSON arrays |
| `nullable` | adds `\|null` | Makes type nullable |

## 🎨 Generated Code Examples

### Private Readonly (Default)
```php
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

### Public Readonly
```php
public function __construct(
    public readonly string $name,
    public readonly int|float $price,
    public readonly int $quantity,
) {}
```

## 🔍 Performance Comparison

| Access Method | Performance | Use Case |
|---------------|-------------|----------|
| Private readonly (getter) | ~0.035s (1M iterations) | Encapsulation, OOP principles |
| Public readonly (direct) | ~0.013s (1M iterations) | Performance, templates, simple containers |

## 📝 Changelog

### Recent Improvements
- ✅ Smart type detection for `numeric` validation
- ✅ Property visibility configuration (`private`/`public` readonly)
- ✅ Constructor property promotion support
- ✅ Comprehensive error handling and type safety
- ✅ Extensive documentation and examples

## 🤝 Contributing

1. Fork the repository
2. Create your feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'Add some amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

## 📄 License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## ⭐ Support

If you find this package helpful, please consider giving it a ⭐ on GitHub!