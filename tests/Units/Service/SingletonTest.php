<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Internal\Service\DatabaseServiceInterface;
use Zotel\Wallet\Internal\Service\MathServiceInterface;
use Zotel\Wallet\Objects\Cart;
use Zotel\Wallet\Test\Infra\PackageModels\WalletTransaction;
use Zotel\Wallet\Test\Infra\PackageModels\WalletTransfer;
use Zotel\Wallet\Test\Infra\PackageModels\Wallet;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class SingletonTest extends TestCase
{
    public function testCart(): void
    {
        self::assertNotSame($this->getRefId(Cart::class), $this->getRefId(Cart::class));
    }

    public function testMathInterface(): void
    {
        self::assertSame($this->getRefId(MathServiceInterface::class), $this->getRefId(MathServiceInterface::class));
    }

    public function testTransaction(): void
    {
        self::assertNotSame($this->getRefId(WalletTransaction::class), $this->getRefId(WalletTransaction::class));
    }

    public function testTransfer(): void
    {
        self::assertNotSame($this->getRefId(WalletTransfer::class), $this->getRefId(WalletTransfer::class));
    }

    public function testWallet(): void
    {
        self::assertNotSame($this->getRefId(Wallet::class), $this->getRefId(Wallet::class));
    }

    public function testDatabaseService(): void
    {
        self::assertSame(
            $this->getRefId(DatabaseServiceInterface::class),
            $this->getRefId(DatabaseServiceInterface::class)
        );
    }

    private function getRefId(string $object): string
    {
        return spl_object_hash(app($object));
    }
}
