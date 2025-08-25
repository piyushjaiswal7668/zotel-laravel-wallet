<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Traits\CanPay;
use Zotel\Wallet\Traits\HasWallets;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class Buyer extends Model implements Customer
{
    use CanPay;
    use HasWallets;

    public function getTable(): string
    {
        return 'users';
    }
}
