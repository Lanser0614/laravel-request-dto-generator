<?php

namespace App\DTOs\Api\v1;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\Api\v1\ModifiersDto;
class ItemsDto extends BaseDto
{

    /**
     * @param string $productId
     * @param string $productName
     * @param int $quantity
     * @param int|float $unitPrice
     * @param int|float $totalPrice
     * @param ModifiersDto[] $modifiers
     */
    public function __construct(
        private readonly string $productId,
        private readonly string $productName,
        private readonly int $quantity,
        private readonly int|float $unitPrice,
        private readonly int|float $totalPrice,
        private readonly ?array $modifiers = null
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
            productId: $data['productId'],
            productName: $data['productName'],
            quantity: $data['quantity'],
            unitPrice: $data['unitPrice'],
            totalPrice: $data['totalPrice'],
            modifiers: array_map(fn($item) => ModifiersDto::fromArray($item), $data['modifiers'] ?? [])
        );
    }

    /**
     * @return string
     */
    public function getProductId(): string
    {
        return $this->productId;
    }

    /**
     * @return string
     */
    public function getProductName(): string
    {
        return $this->productName;
    }

    /**
     * @return int
     */
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * @return int|float
     */
    public function getUnitPrice(): int|float
    {
        return $this->unitPrice;
    }

    /**
     * @return int|float
     */
    public function getTotalPrice(): int|float
    {
        return $this->totalPrice;
    }

    /**
     * @return ModifiersDto[]
     */
    public function getModifiers(): ?array
    {
        return $this->modifiers;
    }
}
