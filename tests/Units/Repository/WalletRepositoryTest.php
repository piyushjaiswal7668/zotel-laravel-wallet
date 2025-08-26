<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Repository;

use Zotel\Wallet\Internal\Repository\WalletRepositoryInterface;
use Zotel\Wallet\Test\Infra\Factories\UserFactory;
use Zotel\Wallet\Test\Infra\PackageModels\Wallet;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class WalletRepositoryTest extends TestCase
{
    public function testUpdateBalancesPreventsSqlInjection(): void
    {
        /** @var WalletRepositoryInterface $repository */
        $repository = app(WalletRepositoryInterface::class);

        $user1 = UserFactory::new()->create();
        $user2 = UserFactory::new()->create();

        $wallet1 = $user1->wallet;
        $wallet2 = $user2->wallet;

        $maliciousId = $wallet1->id.'; DROP TABLE wallet; --';
        $maliciousBalance = '100; DROP TABLE wallet; --';

        $repository->updateBalances([
            $maliciousId => $maliciousBalance,
            $wallet2->id => 200,
        ]);

        self::assertSame(2, Wallet::query()->count());
        self::assertSame('100', $wallet1->fresh()->balance);
        self::assertSame('200', $wallet2->fresh()->balance);
    }
}
