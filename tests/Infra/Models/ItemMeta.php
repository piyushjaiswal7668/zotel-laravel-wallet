<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Models;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Interfaces\ProductLimitedInterface;
use Zotel\Wallet\Models\Wallet;
use Zotel\Wallet\Services\CastService;
use Zotel\Wallet\Traits\HasWallet;
use Zotel\Wallet\Traits\HasWallets;
use Illuminate\Database\Eloquent\Model;

/**
 * @property string $name
 * @property int $quantity
 * @property int $price
 *
 * @method int getKey()
 */
final class ItemMeta extends Model implements ProductLimitedInterface
{
    use HasWallet;
    use HasWallets;

    /**
     * @var array<int, string>
     */
    protected $fillable = ['name', 'quantity', 'price'];

    public function getTable(): string
    {
        return 'items';
    }

    public function canBuy(Customer $customer, int $quantity = 1, bool $force = false): bool
    {
        $result = $this->quantity >= $quantity;

        if ($force) {
            return $result;
        }

        return $result && ! $customer->paid($this) instanceof \Zotel\Wallet\Models\WalletTransfer;
    }

    public function getAmountProduct(Customer $customer): int
    {
        /** @var Wallet $wallet */
        $wallet = app(CastService::class)->getWallet($customer);

        return $this->price + (int) $wallet->holder_id;
    }

    /**
     * @return array{name: string, price: int}
     */
    public function getMetaProduct(): ?array
    {
        return [
            'name' => $this->name,
            'price' => $this->price,
        ];
    }
}
