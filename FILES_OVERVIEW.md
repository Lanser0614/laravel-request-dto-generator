# Laravel Request DTO Generator - Files Overview

## 📁 Project Structure

```
laravel-request-dto-generator/
├── src/
│   ├── BaseDto.php
│   ├── RequestDtoGeneratorServiceProvider.php
│   ├── Commands/
│   │   └── GenerateDtoFromRequestCommand.php
│   ├── Services/
│   │   ├── DtoGeneratorService.php
│   │   └── StubBasedDtoGeneratorService.php
│   └── Traits/
│       └── GeneratesDto.php
├── config/
│   └── request-dto-generator.php
├── stubs/
│   ├── dto.stub
│   ├── property.stub
│   ├── constructor.stub
│   ├── constructor-property-promotion.stub
│   ├── constructor-param.stub
│   └── accessors.stub
├── examples/
│   ├── advanced-validation-examples.php
│   ├── generated-dto-examples.php
│   ├── type-handling-demo.php
│   ├── visibility-demo-simple.php
│   ├── error-examples.php
│   ├── readonly-visibility-demo.php
│   ├── visibility-comparison.php
│   └── usage-examples.md
├── README.md
├── DOCS.md
├── TYPE_ERRORS_GUIDE.md
├── VISIBILITY_GUIDE.md
├── TYPING_IMPROVEMENTS.md
└── FILES_OVERVIEW.md
```

## 📚 Documentation Files

### Main Documentation
- **README.md** - Main package documentation with installation, configuration, and usage examples
- **DOCS.md** - Documentation index with quick navigation and references
- **FILES_OVERVIEW.md** - This file - overview of all project files

### Specialized Guides
- **TYPE_ERRORS_GUIDE.md** - Complete guide to type errors, how to avoid them, and troubleshooting
- **VISIBILITY_GUIDE.md** - Detailed comparison of public vs private readonly properties
- **TYPING_IMPROVEMENTS.md** - Overview of advanced type detection features and improvements

## 📁 Examples Directory

### PHP Examples (Executable)
- **advanced-validation-examples.php** - Complex validation scenarios with various Laravel validation rules
- **generated-dto-examples.php** - Examples of generated DTOs with improved typing
- **type-handling-demo.php** - Complete demonstration of type handling features
- **visibility-demo-simple.php** - Simple demonstration of property visibility differences
- **error-examples.php** - Examples of type errors you might encounter
- **readonly-visibility-demo.php** - Detailed comparison of readonly property visibility
- **visibility-comparison.php** - Comprehensive visibility comparison with performance tests

### Markdown Examples
- **usage-examples.md** - Comprehensive usage examples and best practices

## 🔧 Core Package Files

### Service Provider
- **src/RequestDtoGeneratorServiceProvider.php** - Main service provider that registers services and commands

### Commands
- **src/Commands/GenerateDtoFromRequestCommand.php** - Artisan command for generating DTOs

### Services
- **src/Services/DtoGeneratorService.php** - Core DTO generation logic with improved type detection
- **src/Services/StubBasedDtoGeneratorService.php** - Stub-based DTO generation service

### Base Classes
- **src/BaseDto.php** - Base class for all generated DTOs with utility methods
- **src/Traits/GeneratesDto.php** - Trait for Request classes to easily convert to DTO

### Configuration
- **config/request-dto-generator.php** - Package configuration with all options

### Stubs
- **stubs/dto.stub** - Main DTO template
- **stubs/property.stub** - Property declaration template
- **stubs/constructor.stub** - Constructor template
- **stubs/constructor-property-promotion.stub** - Constructor with property promotion template
- **stubs/constructor-param.stub** - Constructor parameter template
- **stubs/accessors.stub** - Getter/setter methods template

## 🎯 Key Features Implemented

### Smart Type Detection
- `numeric` → `int|float` (numbers only)
- `string` → `string` (strings only)
- `string|numeric` → `string|int|float` (mixed types)
- `boolean` → `bool`
- `array` → `array`
- `nullable` → adds `|null` to type

### Property Visibility Options
- **Private Readonly** (default): Access only through getter methods
- **Public Readonly**: Direct property access + getter methods

### Constructor Property Promotion
- PHP 8+ feature support
- Configurable property visibility
- Clean, concise syntax

### Advanced Features
- Smart class discovery by name
- Multiple namespace support
- Batch generation
- Custom configuration options
- Comprehensive error handling

## 🚀 Usage Examples

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

### Running Examples
```bash
# Run error examples to see type errors
php examples/error-examples.php

# Run visibility demo
php examples/visibility-demo-simple.php

# Run performance comparison
php examples/visibility-comparison.php
```

## 📊 Configuration Options

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

## 🔍 Type Mapping Reference

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

## 📝 Recent Improvements

- ✅ Smart type detection for `numeric` validation
- ✅ Property visibility configuration (`private`/`public` readonly)
- ✅ Constructor property promotion support
- ✅ Comprehensive error handling and type safety
- ✅ Extensive documentation and examples
- ✅ Performance optimization
- ✅ Multiple namespace support
- ✅ Smart class discovery by name

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