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
        private string $couponCode,
        private int|float $discountAmount,
        private string $discountType,
        private bool $isActive,
        private int $usedCount,
        private ?string $expiresAt = null,
        private ?int $usageLimit = null
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
     * @param string $couponCode
     */
    public function setCouponCode(string $couponCode): void
    {
        $this->couponCode = $couponCode;
    }

    /**
     * @return int|float
     */
    public function getDiscountAmount(): int|float
    {
        return $this->discountAmount;
    }

    /**
     * @param int|float $discountAmount
     */
    public function setDiscountAmount(int|float $discountAmount): void
    {
        $this->discountAmount = $discountAmount;
    }

    /**
     * @return string
     */
    public function getDiscountType(): string
    {
        return $this->discountType;
    }

    /**
     * @param string $discountType
     */
    public function setDiscountType(string $discountType): void
    {
        $this->discountType = $discountType;
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
     * @return ?string
     */
    public function getExpiresAt(): ?string
    {
        return $this->expiresAt;
    }

    /**
     * @param ?string $expiresAt
     */
    public function setExpiresAt(?string $expiresAt): void
    {
        $this->expiresAt = $expiresAt;
    }

    /**
     * @return ?int
     */
    public function getUsageLimit(): ?int
    {
        return $this->usageLimit;
    }

    /**
     * @param ?int $usageLimit
     */
    public function setUsageLimit(?int $usageLimit): void
    {
        $this->usageLimit = $usageLimit;
    }

    /**
     * @return int
     */
    public function getUsedCount(): int
    {
        return $this->usedCount;
    }

    /**
     * @param int $usedCount
     */
    public function setUsedCount(int $usedCount): void
    {
        $this->usedCount = $usedCount;
    }
}
