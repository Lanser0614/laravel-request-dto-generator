<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
class ItemsDto extends BaseDto
{

    /**
     * @param string $productId
     * @param string $productName
     * @param int $quantity
     * @param int|float $unitPrice
     * @param int|float $totalPrice
     */
    public function __construct(
        private readonly string $productId,
        private readonly string $productName,
        private readonly int $quantity,
        private readonly int|float $unitPrice,
        private readonly int|float $totalPrice
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
            totalPrice: $data['totalPrice']
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
}
