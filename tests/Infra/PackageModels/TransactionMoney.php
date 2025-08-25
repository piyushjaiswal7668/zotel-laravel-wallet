<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\PackageModels;

use Zotel\Wallet\Test\Infra\Values\Money;

/**
 * Class WalletTransaction.
 *
 * @property Money $currency
 */
final class TransactionMoney extends \Zotel\Wallet\Models\WalletTransaction
{
    private ?Money $currency = null;

    public function getCurrencyAttribute(): Money
    {
        $this->currency ??= new Money($this->amount, $this->meta['currency'] ?? 'USD');

        return $this->currency;
    }
}
