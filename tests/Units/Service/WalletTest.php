<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\ModelNotFoundException;
use Zotel\Wallet\Internal\Service\IdentifierFactoryServiceInterface;
use Zotel\Wallet\Services\WalletServiceInterface;
use Zotel\Wallet\Test\Infra\Factories\BuyerFactory;
use Zotel\Wallet\Test\Infra\Factories\UserMultiFactory;
use Zotel\Wallet\Test\Infra\Models\Buyer;
use Zotel\Wallet\Test\Infra\Models\UserMulti;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class WalletTest extends TestCase
{
    public function testFindBy(): void
    {
        /** @var Buyer $buyer */
        $buyer = BuyerFactory::new()->create();

        $uuidFactoryService = app(IdentifierFactoryServiceInterface::class);
        $walletService = app(WalletServiceInterface::class);

        $uuid = $uuidFactoryService->generate();

        self::assertNull($walletService->findBySlug($buyer, 'default'));
        self::assertNull($walletService->findByUuid($uuid));
        self::assertNull($walletService->findById(-1));

        $buyer->wallet->uuid = $uuid; // @hack
        $buyer->deposit(100);

        self::assertNotNull($walletService->findBySlug($buyer, 'default'));
        self::assertNotNull($walletService->findByUuid($uuid));
        self::assertNotNull($walletService->findById($buyer->wallet->getKey()));
    }

    public function testGetBySlug(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(ExceptionInterface::MODEL_NOT_FOUND);

        /** @var Buyer $buyer */
        $buyer = BuyerFactory::new()->create();
        $walletService = app(WalletServiceInterface::class);

        $walletService->getBySlug($buyer, 'default');
    }

    public function testGetById(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(ExceptionInterface::MODEL_NOT_FOUND);

        app(WalletServiceInterface::class)->getById(-1);
    }

    public function testCreateWalletWithUuid(): void
    {
        /** @var UserMulti $user */
        $user = UserMultiFactory::new()->create();

        $uuidFactoryService = app(IdentifierFactoryServiceInterface::class);

        /** @var string[] $uuids */
        $uuids = array_map(static fn () => $uuidFactoryService->generate(), range(1, 10));

        foreach ($uuids as $uuid) {
            $user->createWallet([
                'uuid' => $uuid,
                'name' => md5($uuid),
            ]);
        }

        self::assertSame(10, $user->wallets()->count());
        self::assertSame(10, $user->wallets()->whereIn('uuid', $uuids)->count());
    }

    public function testGetByUuid(): void
    {
        $this->expectException(ModelNotFoundException::class);
        $this->expectExceptionCode(ExceptionInterface::MODEL_NOT_FOUND);

        $uuidFactoryService = app(IdentifierFactoryServiceInterface::class);

        app(WalletServiceInterface::class)->getByUuid($uuidFactoryService->generate());
    }
}
