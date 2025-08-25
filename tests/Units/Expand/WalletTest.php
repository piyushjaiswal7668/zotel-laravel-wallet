<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Expand;

use Zotel\Wallet\Test\Infra\Factories\BuyerFactory;
use Zotel\Wallet\Test\Infra\Models\Buyer;
use Zotel\Wallet\Test\Infra\PackageModels\MyWallet;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class WalletTest extends TestCase
{
    public function testAddMethod(): void
    {
        config([
            'wallet.wallet.model' => MyWallet::class,
        ]);

        /** @var Buyer $buyer */
        $buyer = BuyerFactory::new()->create();

        /** @var MyWallet $wallet */
        $wallet = $buyer->wallet;

        self::assertSame('hello world', $wallet->helloWorld());
    }
}
