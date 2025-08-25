<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Dto;

use Zotel\Wallet\Interfaces\Customer;

interface AvailabilityDtoInterface
{
    /**
     * Returns the basket DTO object.
     */
    public function getBasketDto(): BasketDtoInterface;

    /**
     * Returns the customer object.
     */
    public function getCustomer(): Customer;

    /**
     * Returns whether the creation is forced.
     */
    public function isForce(): bool;
}
