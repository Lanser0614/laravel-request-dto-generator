<?php

namespace BellissimoPizza\RequestDtoGenerator;

/**
 * Base DTO class that all generated DTOs will extend
 * 
 * This class works with both traditional properties and constructor property promotion.
 * When using constructor property promotion, properties are private readonly and can only
 * be accessed through getter methods or direct property access within the class.
 */
abstract class BaseDto implements \JsonSerializable
{
    /**
     * Convert DTO to array
     */
    public function toArray(): array
    {
        $array = [];
        $reflection = new \ReflectionClass($this);
        
        // Get all properties (public, private, protected)
        $properties = $reflection->getProperties();
        
        foreach ($properties as $property) {
            $propertyName = $property->getName();
            
            // Skip properties that start with underscore (internal properties)
            if (str_starts_with($propertyName, '_')) {
                continue;
            }
            
            // Try to get value using getter method first
            $getterMethod = 'get' . ucfirst($propertyName);
            if ($reflection->hasMethod($getterMethod)) {
                $value = $this->$getterMethod();
            } else {
                // Fallback to direct property access
                try {
                    $property->setAccessible(true);
                    $value = $property->getValue($this);
                } catch (\Throwable $e) {
                    // Skip properties that can't be accessed
                    continue;
                }
            }
            
            if ($value instanceof self) {
                $array[$propertyName] = $value->toArray();
            } elseif (is_array($value)) {
                $array[$propertyName] = array_map(function ($item) {
                    return $item instanceof self ? $item->toArray() : $item;
                }, $value);
            } else {
                $array[$propertyName] = $value;
            }
        }
        
        return $array;
    }

    /**
     * Create DTO from array - must be implemented by each DTO class
     */
    abstract public static function fromArray(array $data): static;

    /**
     * Parse single entity to DTO
     */
    public static function parseEntity(array $data): static
    {
        return static::fromArray($data);
    }

    /**
     * Parse array of entities to array of DTOs
     */
    public static function parseEntities(array $entities): array
    {
        return array_map(function ($entity) {
            return static::fromArray($entity);
        }, $entities);
    }

    /**
     * Convert DTO to JSON
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->toArray(), $options);
    }

    /**
     * Specify data which should be serialized to JSON
     * Implements JsonSerializable interface
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }

    /**
     * Create DTO from JSON
     */
    public static function fromJson(string $json): static
    {
        $data = json_decode($json, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \InvalidArgumentException('Invalid JSON provided');
        }
        
        return static::fromArray($data);
    }

    /**
     * Get all property names
     */
    public function getPropertyNames(): array
    {
        $reflection = new \ReflectionClass($this);
        $properties = [];
        
        foreach ($reflection->getProperties(\ReflectionProperty::IS_PUBLIC) as $property) {
            $properties[] = $property->getName();
        }
        
        return $properties;
    }

    /**
     * Check if property exists
     */
    public function hasProperty(string $name): bool
    {
        return property_exists($this, $name);
    }

    /**
     * Check if property is readonly
     */
    public function isPropertyReadonly(string $name): bool
    {
        if (!$this->hasProperty($name)) {
            return false;
        }
        
        $reflection = new \ReflectionProperty($this, $name);
        return $reflection->isReadOnly();
    }

    /**
     * Get property value
     */
    public function getProperty(string $name): mixed
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '{$name}' does not exist");
        }
        
        return $this->$name;
    }

    /**
     * Set property value
     * Note: This method will throw an exception if the property is readonly
     */
    public function setProperty(string $name, mixed $value): void
    {
        if (!$this->hasProperty($name)) {
            throw new \InvalidArgumentException("Property '{$name}' does not exist");
        }
        
        $reflection = new \ReflectionProperty($this, $name);
        if ($reflection->isReadOnly()) {
            throw new \RuntimeException("Cannot modify readonly property '{$name}'. Readonly properties can only be set in the constructor.");
        }
        
        $this->$name = $value;
    }
}
