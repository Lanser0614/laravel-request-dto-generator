# Versioning Guide

This document explains how versioning works in the Laravel Request DTO Generator package.

## 📋 Version Information

- **Current Version**: 1.0.0
- **Package Name**: bellissimopizza/laravel-request-dto-generator
- **Versioning Scheme**: [Semantic Versioning](https://semver.org/) (SemVer)

## 🎯 Version Components

### Semantic Versioning Format: `MAJOR.MINOR.PATCH`

- **MAJOR** (1): Breaking changes that are not backward compatible
- **MINOR** (0): New features that are backward compatible
- **PATCH** (0): Bug fixes that are backward compatible

### Examples:
- `1.0.0` - Initial release
- `1.0.1` - Bug fix release
- `1.1.0` - New feature release
- `2.0.0` - Breaking change release

## 🚀 Version Commands

### Check Package Version

```bash
# Basic version information
php artisan dto:version

# Full package identifier
php artisan dto:version --full

# JSON output
php artisan dto:version --json
```

### Example Outputs:

```bash
$ php artisan dto:version
Laravel Request DTO Generator
Version: 1.0.0
Package: bellissimopizza/laravel-request-dto-generator

$ php artisan dto:version --full
bellissimopizza/laravel-request-dto-generator@1.0.0

$ php artisan dto:version --json
{
    "name": "bellissimopizza/laravel-request-dto-generator",
    "version": "1.0.0",
    "full_identifier": "bellissimopizza/laravel-request-dto-generator@1.0.0"
}
```

## 🔧 Version Management

### 1. Version Class Usage

```php
use BellissimoPizza\RequestDtoGenerator\Version;

// Get version information
$version = Version::getVersion(); // "1.0.0"
$package = Version::getPackageName(); // "bellissimopizza/laravel-request-dto-generator"
$full = Version::getFullIdentifier(); // "bellissimopizza/laravel-request-dto-generator@1.0.0"

// Version comparisons
Version::isGreaterThan('0.9.0'); // true
Version::isGreaterThanOrEqual('1.0.0'); // true
Version::isLessThan('1.1.0'); // true
Version::equals('1.0.0'); // true

// Get all version info
$info = Version::getInfo();
```

### 2. Updating Versions

#### For Bug Fixes (PATCH):
```bash
# Update composer.json
"version": "1.0.1"

# Update Version.php
const VERSION = '1.0.1';
```

#### For New Features (MINOR):
```bash
# Update composer.json
"version": "1.1.0"

# Update Version.php
const VERSION = '1.1.0';
```

#### For Breaking Changes (MAJOR):
```bash
# Update composer.json
"version": "2.0.0"

# Update Version.php
const VERSION = '2.0.0';
```

## 📦 Package Distribution

### Composer Installation

```bash
# Install specific version
composer require bellissimopizza/laravel-request-dto-generator:1.0.0

# Install latest version
composer require bellissimopizza/laravel-request-dto-generator

# Install with version constraint
composer require bellissimopizza/laravel-request-dto-generator:^1.0
```

### Version Constraints

- `^1.0.0` - Compatible with 1.0.0, allows 1.x.x (but not 2.0.0)
- `~1.0.0` - Compatible with 1.0.0, allows 1.0.x (but not 1.1.0)
- `>=1.0.0 <2.0.0` - Compatible with 1.0.0 and above, but below 2.0.0
- `1.0.*` - Any version in the 1.0.x series

## 🔄 Release Process

### 1. Pre-Release Checklist

- [ ] Update version in `composer.json`
- [ ] Update version in `src/Version.php`
- [ ] Update `CHANGELOG.md`
- [ ] Run tests to ensure everything works
- [ ] Update documentation if needed

### 2. Release Steps

```bash
# 1. Update version
# Edit composer.json and Version.php

# 2. Update changelog
# Edit CHANGELOG.md

# 3. Commit changes
git add .
git commit -m "Release version 1.0.1"

# 4. Create tag
git tag -a v1.0.1 -m "Release version 1.0.1"

# 5. Push changes and tags
git push origin main
git push origin v1.0.1
```

### 3. Post-Release

- [ ] Verify package is available on Packagist
- [ ] Test installation in a fresh Laravel project
- [ ] Update any external documentation

## 📚 Version History

### 1.0.0 (Initial Release)
- Complete DTO generation system
- Smart namespace mapping
- Laravel 9+ compatibility
- Comprehensive documentation
- Test suite and examples

## 🎯 Best Practices

### 1. Version Numbering
- Always use semantic versioning
- Increment the right component based on changes
- Document breaking changes clearly

### 2. Changelog
- Keep changelog up to date
- Use clear, descriptive language
- Group changes by type (Added, Changed, Fixed, Removed)

### 3. Testing
- Test version functionality before release
- Ensure all tests pass
- Test installation in different environments

### 4. Documentation
- Update documentation for new features
- Maintain backward compatibility documentation
- Provide migration guides for breaking changes

## 🔍 Troubleshooting

### Common Issues

1. **Version not updating**: Ensure both `composer.json` and `Version.php` are updated
2. **Command not found**: Make sure the service provider is properly registered
3. **Version mismatch**: Check that all version references are consistent

### Debug Commands

```bash
# Check current version
php artisan dto:version

# Check composer version
composer show bellissimopizza/laravel-request-dto-generator

# Check installed packages
composer list | grep dto-generator
```

## 📞 Support

If you encounter any issues with versioning:

1. Check this guide first
2. Run the version test: `php examples/test-version.php`
3. Check the changelog for recent changes
4. Open an issue on GitHub with version information

---

**Remember**: Always test version changes thoroughly before releasing!
