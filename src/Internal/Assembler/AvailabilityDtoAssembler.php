<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Internal\Dto\AvailabilityDto;
use Zotel\Wallet\Internal\Dto\AvailabilityDtoInterface;
use Zotel\Wallet\Internal\Dto\BasketDtoInterface;

final class AvailabilityDtoAssembler implements AvailabilityDtoAssemblerInterface
{
    public function create(Customer $customer, BasketDtoInterface $basketDto, bool $force): AvailabilityDtoInterface
    {
        return new AvailabilityDto($customer, $basketDto, $force);
    }
}
