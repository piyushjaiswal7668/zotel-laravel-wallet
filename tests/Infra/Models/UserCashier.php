<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Traits\HasWallets;
use Zotel\Wallet\Traits\MorphOneWallet;
use Illuminate\Database\Eloquent\Model;
use Laravel\Cashier\Billable;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class UserCashier extends Model
{
    use Billable;
    use HasWallets;
    use MorphOneWallet;

    public function getTable(): string
    {
        return 'users';
    }
}
