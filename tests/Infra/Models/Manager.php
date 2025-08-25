<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Traits\HasWallet;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property string $email
 *
 * @method int getKey()
 */
final class Manager extends Model implements Wallet
{
    use HasWallet;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'email'];
}
