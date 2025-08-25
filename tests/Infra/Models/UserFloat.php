<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Interfaces\WalletFloat;
use Zotel\Wallet\Traits\HasWalletFloat;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class UserFloat extends Model implements Wallet, WalletFloat
{
    use HasWalletFloat;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email'];

    public function getTable(): string
    {
        return 'users';
    }
}
