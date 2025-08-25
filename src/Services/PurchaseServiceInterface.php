<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Internal\Dto\BasketDtoInterface;
use Zotel\Wallet\Models\WalletTransfer;

/**
 * @api
 */
interface PurchaseServiceInterface
{
    /**
     * Retrieve an array of already purchased transfers for a given customer and basket.
     *
     * This method retrieves an array of already purchased transfers for a given customer and basket.
     * The customer and basket are defined by the Customer and BasketDtoInterface objects respectively.
     *
     * @param Customer $customer The customer to retrieve transfers for.
     * @param BasketDtoInterface $basketDto The basket to retrieve transfers for.
     * @param bool $gifts [optional] Whether to only retrieve gift transfers or not. Default is false.
     * @return WalletTransfer[] An array of already purchased transfers.
     */
    public function already(Customer $customer, BasketDtoInterface $basketDto, bool $gifts = false): array;
}
