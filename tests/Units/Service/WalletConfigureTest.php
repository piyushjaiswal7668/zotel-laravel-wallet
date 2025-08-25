<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Test\Infra\TestCase;
use Zotel\Wallet\WalletConfigure;

/**
 * @internal
 */
final class WalletConfigureTest extends TestCase
{
    public function testIgnoreMigrations(): void
    {
        self::assertTrue(WalletConfigure::isRunsMigrations());

        WalletConfigure::ignoreMigrations();
        self::assertFalse(WalletConfigure::isRunsMigrations());

        WalletConfigure::reset();
        self::assertTrue(WalletConfigure::isRunsMigrations());
    }
}
