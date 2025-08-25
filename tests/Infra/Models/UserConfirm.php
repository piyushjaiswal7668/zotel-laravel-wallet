<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Confirmable;
use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Traits\CanConfirm;
use Zotel\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class UserConfirm extends Model implements Wallet, Confirmable
{
    use HasWallet;
    use CanConfirm;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email'];

    public function getTable(): string
    {
        return 'users';
    }
}
