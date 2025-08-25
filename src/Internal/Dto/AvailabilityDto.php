<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Dto;

use Zotel\Wallet\Interfaces\Customer;

/** @immutable */
final readonly class AvailabilityDto implements AvailabilityDtoInterface
{
    public function __construct(
        private Customer $customer,
        private BasketDtoInterface $basketDto,
        private bool $force
    ) {
    }

    public function getBasketDto(): BasketDtoInterface
    {
        return $this->basketDto;
    }

    public function getCustomer(): Customer
    {
        return $this->customer;
    }

    public function isForce(): bool
    {
        return $this->force;
    }
}
