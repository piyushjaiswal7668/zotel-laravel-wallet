<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Interfaces\Discount;
use Zotel\Wallet\Interfaces\Wallet;

/**
 * @internal
 */
final class DiscountService implements DiscountServiceInterface
{
    public function getDiscount(Wallet $customer, Wallet $product): int
    {
        if (! $customer instanceof Customer) {
            return 0;
        }
        if (! $product instanceof Discount) {
            return 0;
        }

        return (int) $product->getPersonalDiscount($customer);
    }
}
