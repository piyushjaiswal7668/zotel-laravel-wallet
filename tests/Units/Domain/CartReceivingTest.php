<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Domain;

use App\Models\WalletTransfer;
use Zotel\Wallet\Objects\Cart;
use Zotel\Wallet\Services\PurchaseServiceInterface;
use Zotel\Wallet\Test\Infra\Factories\BuyerFactory;
use Zotel\Wallet\Test\Infra\Factories\ItemFactory;
use Zotel\Wallet\Test\Infra\Factories\ItemMetaFactory;
use Zotel\Wallet\Test\Infra\Models\Buyer;
use Zotel\Wallet\Test\Infra\Models\Item;
use Zotel\Wallet\Test\Infra\Models\ItemMeta;
use Zotel\Wallet\Test\Infra\PackageModels\WalletTransaction;
use Zotel\Wallet\Test\Infra\TestCase;
use function count;
use Illuminate\Database\Eloquent\Collection;

/**
 * @internal
 */
final class CartReceivingTest extends TestCase
{
    public function testCartMeta(): void
    {
        /** @var Buyer $buyer */
        $buyer = BuyerFactory::new()->create();
        /** @var ItemMeta $product */
        $product = ItemMetaFactory::new()->create([
            'quantity' => 1,
        ]);

        $expected = 'pay';

        $payment = $buyer->createWallet([
            'name' => 'Dollar',
            'meta' => [
                'currency' => 'USD',
            ],
        ]);

        $receiving = $product->createWallet([
            'name' => 'Dollar',
            'meta' => [
                'currency' => 'USD',
            ],
        ]);

        $cart = app(Cart::class)
            ->withItem($product, receiving: $receiving)
            ->withMeta([
                'type' => $expected,
            ]);

        $amount = $cart->getTotal($buyer);

        self::assertSame(0, $buyer->balanceInt);
        self::assertNotNull($payment->deposit($amount));

        $transfers = $payment->payCart($cart);
        self::assertCount(1, $transfers);

        $transfer = current($transfers);

        /** @var WalletTransaction[] $transactions */
        $transactions = [$transfer->deposit, $transfer->withdraw];
        foreach ($transactions as $transaction) {
            self::assertSame($product->price, $transaction->meta['price']);
            self::assertSame($product->name, $transaction->meta['name']);
            self::assertSame($expected, $transaction->meta['type']);
        }

        self::assertSame((int) $amount, $receiving->balanceInt);
        self::assertSame('USD', $receiving->currency);

        self::assertSame(0, $payment->balanceInt);
        self::assertSame('USD', $payment->currency);
    }

    public function testPay(): void
    {
        /** @var Buyer $buyer */
        $buyer = BuyerFactory::new()->create();
        /** @var Collection<int, Item> $products */
        $products = ItemFactory::times(10)->create([
            'quantity' => 1,
        ]);

        $cart = app(Cart::class);
        foreach ($products as $product) {
            $receiving = $product->createWallet([
                'name' => 'Dollar',
                'meta' => [
                    'currency' => 'USD',
                ],
            ]);

            $cart = $cart->withItem($product, pricePerItem: 1, receiving: $receiving);
        }

        self::assertCount(10, $cart->getItems());

        foreach ($cart->getItems() as $product) {
            /** @var Item $product */
            self::assertSame(0, $product->getWallet('dollar')?->balanceInt);
        }

        $payment = $buyer->createWallet([
            'name' => 'Dollar',
            'meta' => [
                'currency' => 'USD',
            ],
        ]);

        $payment->deposit($cart->getTotal($buyer));

        self::assertSame(10, $payment->balanceInt);

        $transfers = $payment->payCart($cart);

        self::assertCount(count($cart), $transfers);
        self::assertTrue((bool) app(PurchaseServiceInterface::class)->already($payment, $cart->getBasketDto()));
        self::assertSame(0, $payment->balanceInt);

        foreach ($transfers as $transfer) {
            self::assertSame(WalletTransfer::STATUS_PAID, $transfer->status);
            self::assertNull($transfer->status_last);
        }

        foreach ($cart->getItems() as $product) {
            /** @var Item $product */
            self::assertSame(1, $product->getWallet('dollar')?->balanceInt);
        }

        self::assertTrue($payment->refundCart($cart));
        foreach ($transfers as $transfer) {
            $transfer->refresh();
            self::assertSame(WalletTransfer::STATUS_REFUND, $transfer->status);
            self::assertSame(WalletTransfer::STATUS_PAID, $transfer->status_last);
        }

        self::assertSame(10, $payment->balanceInt);
    }
}
