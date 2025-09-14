# Changelog

All notable changes to the Laravel Request DTO Generator package will be documented in this file.

## [Unreleased]

### Added
- Smart type detection for Laravel validation rules
- Property visibility configuration (`private`/`public` readonly)
- Constructor property promotion support
- Comprehensive error handling and type safety
- Extensive documentation and examples
- Performance optimization
- Multiple namespace support
- Smart class discovery by name

### Changed
- Improved type detection for `numeric` validation (now returns `int|float` instead of `string|int|float`)
- Enhanced error messages with detailed type information
- Updated configuration with new `property_visibility` option
- Improved stub templates for better code generation

### Fixed
- Type conflicts in mixed validation scenarios
- Error handling for readonly property modifications
- Configuration validation and defaults

## [1.0.0] - Initial Release

### Added
- Basic DTO generation from Laravel Request classes
- Artisan command for DTO generation
- Base DTO class with utility methods
- Stub-based template system
- Configuration options for customization
- Trait for Request classes to convert to DTO
- Basic type detection from validation rules
- Batch generation for all Request classes

### Features
- Automatic DTO generation from existing Request classes
- Flexible configuration options
- Stub-based templates for customization
- Artisan commands for easy usage
- Base DTO class with utility methods
- Trait support for Request classes

## Detailed Changes

### Smart Type Detection Improvements

#### Before
```php
// numeric validation was typed as string|int|float
'price' => 'required|numeric', // string|int|float
```

#### After
```php
// numeric validation is now typed as int|float
'price' => 'required|numeric', // int|float
```

### Property Visibility Configuration

#### New Configuration Option
```php
// config/request-dto-generator.php
'property_visibility' => 'private', // Default: private readonly
'property_visibility' => 'public',  // Alternative: public readonly
```

#### Generated Code Examples

**Private Readonly (Default)**
```php
public function __construct(
    private readonly string $name,
    private readonly int|float $price,
    private readonly int $quantity,
) {}
```

**Public Readonly**
```php
public function __construct(
    public readonly string $name,
    public readonly int|float $price,
    public readonly int $quantity,
) {}
```

### Enhanced Type Detection

| Laravel Validation | Old Type | New Type | Description |
|-------------------|----------|----------|-------------|
| `numeric` | `string\|int\|float` | `int\|float` | Numbers only |
| `string` | `string` | `string` | Strings only |
| `string\|numeric` | `string\|int\|float` | `string\|int\|float` | Mixed types |
| `boolean` | `bool` | `bool` | Boolean values |
| `accepted` | `bool` | `bool` | Boolean values |
| `email` | `string` | `string` | Email strings |
| `url` | `string` | `string` | URL strings |
| `uuid` | `string` | `string` | UUID strings |
| `ip` | `string` | `string` | IP address strings |
| `date` | `string` | `string` | Date strings |
| `array` | `array` | `array` | Arrays |
| `json` | `array` | `array` | JSON arrays |
| `nullable` | adds `\|null` | adds `\|null` | Makes type nullable |

### Smart Class Discovery

#### Before
```bash
# Had to specify full class name
php artisan dto:generate App\\Http\\Requests\\CreateUserRequest
```

#### After
```bash
# Can use just the class name
php artisan dto:generate CreateUserRequest

# If multiple classes found, you'll be prompted to choose
Found multiple Request classes with the same name:

1. App\Http\Requests\CreateUserRequest
2. App\Admin\Requests\CreateUserRequest

Please select which class to use (1-2): 1
```

### Enhanced Error Handling

#### Type Errors
```php
// Clear error messages for type mismatches
TypeError: CreateProductDto::__construct(): 
Argument #1 ($price) must be of type int|float, string given
```

#### Readonly Property Errors
```php
// Clear error messages for readonly property modifications
RuntimeException: Cannot modify readonly property 'price'. 
Readonly properties can only be set in the constructor.
```

### Performance Improvements

| Access Method | Performance | Improvement |
|---------------|-------------|-------------|
| Private readonly (getter) | ~0.035s (1M iterations) | Baseline |
| Public readonly (direct) | ~0.013s (1M iterations) | 167% faster |

### Documentation Enhancements

#### Added Documentation Files
- `TYPE_ERRORS_GUIDE.md` - Complete guide to type errors
- `VISIBILITY_GUIDE.md` - Detailed comparison of property visibility
- `TYPING_IMPROVEMENTS.md` - Overview of type detection features
- `DOCS.md` - Documentation index
- `FILES_OVERVIEW.md` - Project files overview
- `CHANGELOG.md` - This changelog

#### Added Example Files
- `examples/advanced-validation-examples.php` - Complex validation scenarios
- `examples/generated-dto-examples.php` - Generated DTO examples
- `examples/type-handling-demo.php` - Type handling demonstration
- `examples/visibility-demo-simple.php` - Property visibility demo
- `examples/error-examples.php` - Type error examples
- `examples/readonly-visibility-demo.php` - Detailed visibility comparison
- `examples/visibility-comparison.php` - Performance comparison
- `examples/usage-examples.md` - Usage examples and best practices

### Configuration Updates

#### New Configuration Options
```php
// config/request-dto-generator.php
return [
    // ... existing options ...
    
    // New option for property visibility
    'property_visibility' => 'private', // 'private' or 'public'
];
```

#### Updated Stub Templates
- `stubs/constructor-param.stub` - Now supports configurable visibility
- Enhanced template variables for better customization

### Command Enhancements

#### New Command Features
- Smart class discovery by name
- Multiple namespace support
- Interactive class selection
- Enhanced error messages
- Better validation and error handling

#### Command Usage Examples
```bash
# Basic usage with smart discovery
php artisan dto:generate CreateUserRequest

# Batch generation
php artisan dto:generate --all

# Force overwrite
php artisan dto:generate --all --force

# Custom namespace
php artisan dto:generate CreateUserRequest --namespace="App\\DataTransferObjects"

# Custom directory
php artisan dto:generate CreateUserRequest --directory="/custom/dto/path"
```

## Migration Guide

### From Previous Versions

#### 1. Update Configuration
If you have a custom configuration, add the new `property_visibility` option:

```php
// config/request-dto-generator.php
'property_visibility' => 'private', // Add this line
```

#### 2. Regenerate DTOs
After updating the configuration, regenerate your DTOs:

```bash
php artisan dto:generate --all --force
```

#### 3. Update Code (if needed)
If you were using direct property access and want to switch to private readonly:

```php
// Before (if using public readonly)
$name = $dto->name;

// After (with private readonly)
$name = $dto->getName();
```

### Breaking Changes

#### Type Changes
- `numeric` validation now generates `int|float` instead of `string|int|float`
- This may require updating code that expected string values from numeric fields

#### Property Visibility
- Default property visibility is now `private` (was effectively `public` before)
- This may require updating code that used direct property access

## Future Plans

### Planned Features
- [ ] Support for custom type mappings
- [ ] Integration with popular IDEs
- [ ] Advanced validation rule parsing
- [ ] Support for nested DTOs
- [ ] Custom getter/setter generation
- [ ] Integration with Laravel's type system

### Potential Improvements
- [ ] Better performance optimization
- [ ] More comprehensive error handling
- [ ] Enhanced documentation
- [ ] More example scenarios
- [ ] Integration with testing frameworks

## Contributing

We welcome contributions! Please see our [Contributing Guide](README.md#contributing) for details.

## Support

If you encounter any issues or have questions, please:
1. Check the [documentation](DOCS.md)
2. Review the [examples](examples/)
3. Open an issue on GitHub
4. Consider contributing a fix or improvement

## License

This package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).