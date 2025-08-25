<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Interfaces\WalletFloat;
use Zotel\Wallet\Traits\HasWalletFloat;
use Zotel\Wallet\Traits\HasWallets;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class UserMulti extends Model implements Wallet, WalletFloat
{
    use HasWalletFloat;
    use HasWallets;

    public function getTable(): string
    {
        return 'users';
    }
}
