<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\PackageModels;

/**
 * Class WalletTransaction.
 *
 * @property null|string $bank_method
 */
final class WalletTransaction extends \App\Models\WalletTransaction
{
    public function getFillable(): array
    {
        return array_merge($this->fillable, ['bank_method']);
    }
}
