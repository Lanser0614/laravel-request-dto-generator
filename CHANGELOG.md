# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

## [2.1.0] - 2024-01-XX

### Added
- Smart Namespace Mapping feature
- Version command (`dto:version`)
- Comprehensive test suite
- Documentation for namespace mapping
- Version management system
- Professional package versioning

### Changed
- Updated to version 2.1.0
- Enhanced namespace mapping functionality
- Improved documentation structure

## [1.0.0] - 2024-01-XX

### Added
- Initial release of Laravel Request DTO Generator
- Automatic DTO generation from Laravel Request classes
- Support for complex nested structures and typed arrays
- PHP 8+ features support (readonly properties, union types, constructor property promotion)
- Laravel 9.x, 10.x, and 11.x compatibility
- Smart class discovery for Request classes in subdirectories
- Flexible search patterns for finding Request classes
- JSON Schema-based DTO generation
- ValidationSchemaGenerator for processing Laravel validation rules
- Comprehensive configuration options
- Artisan command integration (`dto:generate`)
- Support for multiple DTO generation strategies
- BaseDto class with common functionality
- Extensive documentation and examples

### Features
- **Smart Namespace Mapping**: Automatically maps Request class namespaces to DTO namespaces
  - `App\Http\Requests\TestRequest` → `App\DTOs\TestDto`
  - `App\Http\Requests\Api\SentCouponRequest` → `App\DTOs\Api\SentCouponDto`
  - `App\Http\Requests\Coupon\CreateCouponRequest` → `App\DTOs\Coupon\CreateCouponDto`

- **Smart Class Discovery**: Finds Request classes across different namespaces and subdirectories
- **Flexible Search Patterns**: Supports various naming conventions
- **Advanced Type Detection**: Handles complex nested structures and typed arrays
- **Laravel Integration**: Seamless integration with Laravel's validation system

### Technical Details
- PHP 8.0+ requirement
- Laravel 9.x, 10.x, 11.x support
- PSR-4 autoloading
- MIT License
- Comprehensive test coverage
- Extensive documentation

---

## Version History

### 1.0.0 (Initial Release)
- Complete DTO generation system
- Smart namespace mapping
- Laravel 9+ compatibility
- Comprehensive documentation
- Test suite and examples

---

## Contributing

Please see [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.