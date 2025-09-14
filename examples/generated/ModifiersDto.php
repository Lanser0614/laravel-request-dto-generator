<?php

namespace App\DTOs\Api\v1;

use BellissimoPizza\RequestDtoGenerator\BaseDto;
class ModifiersDto extends BaseDto
{

    /**
     * @param string $modifierId
     * @param string $name
     * @param int|float $price
     */
    public function __construct(
        private readonly string $modifierId,
        private readonly string $name,
        private readonly int|float $price
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
            modifierId: $data['modifierId'],
            name: $data['name'],
            price: $data['price']
        );
    }

    /**
     * @return string
     */
    public function getModifierId(): string
    {
        return $this->modifierId;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return int|float
     */
    public function getPrice(): int|float
    {
        return $this->price;
    }
}
