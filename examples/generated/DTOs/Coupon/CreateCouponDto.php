<?php

namespace App\DTOs\Coupon;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
use App\DTOs\Coupon\ApplicableProductsDto;
use App\DTOs\Coupon\CustomerDto;
class CreateCouponDto extends BaseDto
{

    /**
     * @param string $code
     * @param string $discountType
     * @param int|float $discountValue
     * @param int|float|null $minOrderAmount
     * @param int|float|null $maxDiscountAmount
     * @param ?int $usageLimit
     * @param ?string $expiresAt
     * @param bool $isActive
     * @param ?string $description
     * @param ApplicableProductsDto[] $applicableProducts
     * @param CustomerDto[] $customer
     */
    public function __construct(
        private string $code,
        private string $discountType,
        private int|float $discountValue,
        private bool $isActive,
        private array $applicableProducts,
        private array $customer,
        private int|float|null $minOrderAmount = null,
        private int|float|null $maxDiscountAmount = null,
        private ?int $usageLimit = null,
        private ?string $expiresAt = null,
        private ?string $description = null
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
            code: $data['code'],
            discountType: $data['discountType'],
            discountValue: $data['discountValue'],
            isActive: $data['isActive'],
            applicableProducts: array_map(fn($item) => ApplicableProductsDto::fromArray($item), $data['applicableProducts'] ?? []),
            customer: array_map(fn($item) => CustomerDto::fromArray($item), $data['customer'] ?? []),
            minOrderAmount: $data['minOrderAmount'] ?? null,
            maxDiscountAmount: $data['maxDiscountAmount'] ?? null,
            usageLimit: $data['usageLimit'] ?? null,
            expiresAt: $data['expiresAt'] ?? null,
            description: $data['description'] ?? null
        );
    }

    /**
     * @return string
     */
    public function getCode(): string
    {
        return $this->code;
    }

    /**
     * @param string $code
     */
    public function setCode(string $code): void
    {
        $this->code = $code;
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
     * @return int|float
     */
    public function getDiscountValue(): int|float
    {
        return $this->discountValue;
    }

    /**
     * @param int|float $discountValue
     */
    public function setDiscountValue(int|float $discountValue): void
    {
        $this->discountValue = $discountValue;
    }

    /**
     * @return int|float|null
     */
    public function getMinOrderAmount(): int|float|null
    {
        return $this->minOrderAmount;
    }

    /**
     * @param int|float|null $minOrderAmount
     */
    public function setMinOrderAmount(int|float|null $minOrderAmount): void
    {
        $this->minOrderAmount = $minOrderAmount;
    }

    /**
     * @return int|float|null
     */
    public function getMaxDiscountAmount(): int|float|null
    {
        return $this->maxDiscountAmount;
    }

    /**
     * @param int|float|null $maxDiscountAmount
     */
    public function setMaxDiscountAmount(int|float|null $maxDiscountAmount): void
    {
        $this->maxDiscountAmount = $maxDiscountAmount;
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
    public function getDescription(): ?string
    {
        return $this->description;
    }

    /**
     * @param ?string $description
     */
    public function setDescription(?string $description): void
    {
        $this->description = $description;
    }

    /**
     * @return ApplicableProductsDto[]
     */
    public function getApplicableProducts(): array
    {
        return $this->applicableProducts;
    }

    /**
     * @param array $applicableProducts
     */
    public function setApplicableProducts(array $applicableProducts): void
    {
        $this->applicableProducts = $applicableProducts;
    }

    /**
     * @return CustomerDto[]
     */
    public function getCustomer(): array
    {
        return $this->customer;
    }

    /**
     * @param array $customer
     */
    public function setCustomer(array $customer): void
    {
        $this->customer = $customer;
    }
}
