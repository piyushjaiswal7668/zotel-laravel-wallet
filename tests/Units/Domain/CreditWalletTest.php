<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Domain;

use Zotel\Wallet\Services\AtomicServiceInterface;
use Zotel\Wallet\Test\Infra\Factories\BuyerFactory;
use Zotel\Wallet\Test\Infra\Factories\UserMultiFactory;
use Zotel\Wallet\Test\Infra\Models\Buyer;
use Zotel\Wallet\Test\Infra\Models\UserMulti;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class CreditWalletTest extends TestCase
{
    /**
     * @see https://github.com/bavix/laravel-wallet/issues/397
     */
    public function testCreditLimit(): void
    {
        /** @var UserMulti $user */
        $user = UserMultiFactory::new()->create();
        $wallet = $user->createWallet([
            'name' => 'Credit USD',
            'slug' => 'credit-usd',
            'meta' => [
                'credit' => 10000,
                'currency' => 'USD',
            ],
        ]);

        $transaction = $wallet->deposit(1000);
        self::assertNotNull($transaction);

        self::assertSame(1000, $wallet->balanceInt);
        self::assertSame(10., (float) $wallet->balanceFloat);

        $transaction = $wallet->withdraw(10000);
        self::assertNotNull($transaction);
        self::assertSame(-9000, $wallet->balanceInt);
    }

    public function testCreditLimitBalanceZero(): void
    {
        /** @var UserMulti $user */
        $user = UserMultiFactory::new()->create();
        $wallet = $user->createWallet([
            'name' => 'Credit USD',
            'slug' => 'credit-usd',
            'meta' => [
                'credit' => 10000,
                'currency' => 'USD',
            ],
        ]);

        self::assertSame(0, $wallet->balanceInt);
        self::assertSame(0., (float) $wallet->balanceFloat);

        $transaction = $wallet->withdraw(10000);
        self::assertNotNull($transaction);
        self::assertSame(-10000, $wallet->balanceInt);
    }

    public function testFrozenBalance(): void
    {
        /** @var Buyer $user */
        $user = BuyerFactory::new()->create();

        self::assertFalse($user->relationLoaded('wallet'));
        self::assertEquals(0, $user->wallet->balanceInt);

        app(AtomicServiceInterface::class)->block($user, function () use ($user) {
            $user->deposit(1000);

            $meta = $user->wallet->meta ?? [];
            $meta['credit'] = ($meta['credit'] ?? 0) - 1000;

            $user->wallet->meta = $meta;
            $user->wallet->saveOrFail();
        });

        self::assertEquals(1000, $user->wallet->balanceInt);
        self::assertEquals(-1000.0, (float) $user->wallet->getCreditAttribute());

        self::assertFalse($user->canWithdraw(1));
    }
}
