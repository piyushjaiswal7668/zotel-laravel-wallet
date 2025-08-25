<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Interfaces\ProductLimitedInterface;
use App\Models\WalletTransfer;
use App\Models\Wallet;
use Zotel\Wallet\Services\CastService;
use Zotel\Wallet\Services\CastServiceInterface;
use Zotel\Wallet\Test\Infra\Helpers\Config;
use Zotel\Wallet\Traits\HasWallet;
use Zotel\Wallet\Traits\HasWallets;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * @property string $name
 * @property int $quantity
 * @property int $price
 *
 * @method int getKey()
 */
final class Item extends Model implements ProductLimitedInterface
{
    use HasWallet;
    use HasWallets;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'quantity', 'price'];

    public function canBuy(Customer $customer, int $quantity = 1, bool $force = false): bool
    {
        $result = $this->quantity >= $quantity;

        if ($force) {
            return $result;
        }

        return $result && ! $customer->paid($this) instanceof WalletTransfer;
    }

    public function getAmountProduct(Customer $customer): int
    {
        /** @var Wallet $wallet */
        $wallet = app(CastService::class)->getWallet($customer);

        return $this->price + (int) $wallet->holder_id;
    }

    public function getMetaProduct(): ?array
    {
        return null;
    }

    /**
     * @param int[] $walletIds
     * @return HasMany<WalletTransfer>
     */
    public function boughtGoods(array $walletIds): HasMany
    {
        return app(CastServiceInterface::class)
            ->getWallet($this)
            ->hasMany(Config::classString('wallet.transfer.model', WalletTransfer::class), 'to_id')
            ->where('status', WalletTransfer::STATUS_PAID)
            ->whereIn('from_id', $walletIds);
    }
}
