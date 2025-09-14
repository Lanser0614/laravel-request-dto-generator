<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\ItemsDto;
use App\DTOs\PaymentsDto;
class SimpleOrderDto extends BaseDto
{

    /**
     * @param string $orderNumber
     * @param string $orderDate
     * @param int|float $totalAmount
     * @param bool $isPaid
     * @param ItemsDto[] $items
     * @param PaymentsDto[] $payments
     * @param ?string $notes
     */
    public function __construct(
        private readonly string $orderNumber,
        private readonly string $orderDate,
        private readonly int|float $totalAmount,
        private readonly bool $isPaid,
        private readonly array $items,
        private readonly ?array $payments = null,
        private readonly ?string $notes = null
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
            orderNumber: $data['orderNumber'],
            orderDate: $data['orderDate'],
            totalAmount: $data['totalAmount'],
            isPaid: $data['isPaid'],
            items: array_map(fn($item) => ItemsDto::fromArray($item), $data['items'] ?? []),
            payments: array_map(fn($item) => PaymentsDto::fromArray($item), $data['payments'] ?? []),
            notes: $data['notes'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getOrderNumber(): string
    {
        return $this->orderNumber;
    }

    /**
     * @return string
     */
    public function getOrderDate(): string
    {
        return $this->orderDate;
    }

    /**
     * @return int|float
     */
    public function getTotalAmount(): int|float
    {
        return $this->totalAmount;
    }

    /**
     * @return bool
     */
    public function getIsPaid(): bool
    {
        return $this->isPaid;
    }

    /**
     * @return ItemsDto[]
     */
    public function getItems(): array
    {
        return $this->items;
    }

    /**
     * @return PaymentsDto[]
     */
    public function getPayments(): ?array
    {
        return $this->payments;
    }

    /**
     * @return ?string
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }
}
