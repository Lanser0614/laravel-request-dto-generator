<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\ItemsDto;
class TestDto extends BaseDto
{

    /**
     * @param string $name
     * @param string $email
     * @param int $age
     * @param bool $isActive
     * @param ItemsDto[] $items
     */
    public function __construct(
        private string $name,
        private string $email,
        private int $age,
        private bool $isActive,
        private array $items
    ) {}

    /**
     * Create DTO from array
     *
     * @param array $data
     * @return static
     */
    public static function fromArray(array $data): static
    {
        return new static(
            name: $data['name'],
            email: $data['email'],
            age: $data['age'],
            isActive: $data['isActive'],
            items: array_map(fn($item) => ItemsDto::fromArray($item), $data['items'] ?? [])
        );
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @param string $email
     */
    public function setEmail(string $email): void
    {
        $this->email = $email;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @param int $age
     */
    public function setAge(int $age): void
    {
        $this->age = $age;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @param bool $isActive
     */
    public function setIsActive(bool $isActive): void
    {
        $this->isActive = $isActive;
    }

    /**
     * @return ItemsDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @param array $items
     */
    public function setItems(array $items): void
    {
        $this->items = $items;
    }
}
