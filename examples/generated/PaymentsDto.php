<?php

namespace App\DTOs;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
class PaymentsDto extends BaseDto
{

    /**
     * @param string $paymentId
     * @param int|float $amount
     * @param string $method
     * @param ?string $transactionId
     */
    public function __construct(
        private readonly string $paymentId,
        private readonly int|float $amount,
        private readonly string $method,
        private readonly ?string $transactionId = null
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
            paymentId: $data['paymentId'],
            amount: $data['amount'],
            method: $data['method'],
            transactionId: $data['transactionId'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getPaymentId(): string
    {
        return $this->paymentId;
    }

    /**
     * @return int|float
     */
    public function getAmount(): int|float
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getMethod(): string
    {
        return $this->method;
    }

    /**
     * @return ?string
     */
    public function getTransactionId(): ?string
    {
        return $this->transactionId;
    }
}
