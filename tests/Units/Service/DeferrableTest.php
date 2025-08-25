<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Test\Infra\TestCase;
use Zotel\Wallet\WalletServiceProvider;
use Illuminate\Contracts\Support\DeferrableProvider;

/**
 * @internal
 */
final class DeferrableTest extends TestCase
{
    public function testCheckDeferrableProvider(): void
    {
        $walletServiceProvider = app()
            ->resolveProvider(WalletServiceProvider::class);

        self::assertInstanceOf(DeferrableProvider::class, $walletServiceProvider);
        self::assertNotEmpty($walletServiceProvider->provides());
    }
}
