<?php

namespace App\DTOs\Api\v1;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\Api\v1\ItemsDto;
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
        private readonly string $name,
        private readonly string $email,
        private readonly int $age,
        private readonly bool $isActive,
        private readonly array $items
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
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return int
     */
    public function getAge(): int
    {
        return $this->age;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return ItemsDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }
}
