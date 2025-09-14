<?php

namespace App\DTOs\Api;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
class SentCouponDto extends BaseDto
{

    /**
     * @param string $couponCode
     * @param int|float $discountAmount
     * @param string $discountType
     * @param bool $isActive
     * @param ?string $expiresAt
     * @param ?int $usageLimit
     * @param int $usedCount
     */
    public function __construct(
        private readonly string $couponCode,
        private readonly int|float $discountAmount,
        private readonly string $discountType,
        private readonly bool $isActive,
        private readonly int $usedCount,
        private readonly ?string $expiresAt = null,
        private readonly ?int $usageLimit = null
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
            couponCode: $data['couponCode'],
            discountAmount: $data['discountAmount'],
            discountType: $data['discountType'],
            isActive: $data['isActive'],
            usedCount: $data['usedCount'],
            expiresAt: $data['expiresAt'] ?? null,
            usageLimit: $data['usageLimit'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getCouponCode(): string
    {
        return $this->couponCode;
    }

    /**
     * @return int|float
     */
    public function getDiscountAmount(): int|float
    {
        return $this->discountAmount;
    }

    /**
     * @return string
     */
    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    /**
     * @return bool
     */
    public function getIsActive(): bool
    {
        return $this->isActive;
    }

    /**
     * @return ?string
     */
    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
    }

    /**
     * @return ?int
     */
    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    /**
     * @return int
     */
    public function getUsedCount(): int
    {
        return $this->usedCount;
    }
}
