<?php

namespace BellissimoPizza\RequestDtoGenerator;

class Version
{
    /**
     * The package version
     */
    const VERSION = '2.1.0';

    /**
     * The package name
     */
    const PACKAGE_NAME = 'bellissimopizza/laravel-request-dto-generator';

    /**
     * Get the package version
     */
    public static function getVersion(): string
    {
        return self::VERSION;
    }

    /**
     * Get the package name
     */
    public static function getPackageName(): string
    {
        return self::PACKAGE_NAME;
    }

    /**
     * Get the full package identifier with version
     */
    public static function getFullIdentifier(): string
    {
        return self::PACKAGE_NAME . '@' . self::VERSION;
    }

    /**
     * Check if the current version is greater than the given version
     */
    public static function isGreaterThan(string $version): bool
    {
        return version_compare(self::VERSION, $version, '>');
    }

    /**
     * Check if the current version is greater than or equal to the given version
     */
    public static function isGreaterThanOrEqual(string $version): bool
    {
        return version_compare(self::VERSION, $version, '>=');
    }

    /**
     * Check if the current version is less than the given version
     */
    public static function isLessThan(string $version): bool
    {
        return version_compare(self::VERSION, $version, '<');
    }

    /**
     * Check if the current version is less than or equal to the given version
     */
    public static function isLessThanOrEqual(string $version): bool
    {
        return version_compare(self::VERSION, $version, '<=');
    }

    /**
     * Check if the current version equals the given version
     */
    public static function equals(string $version): bool
    {
        return version_compare(self::VERSION, $version, '==');
    }

    /**
     * Get version information as array
     */
    public static function getInfo(): array
    {
        return [
            'name' => self::PACKAGE_NAME,
            'version' => self::VERSION,
            'full_identifier' => self::getFullIdentifier(),
        ];
    }
}
