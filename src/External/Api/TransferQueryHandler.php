<?php

declare(strict_types=1);

namespace Zotel\Wallet\External\Api;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Models\WalletTransfer;
use Zotel\Wallet\Services\AssistantServiceInterface;
use Zotel\Wallet\Services\AtomicServiceInterface;
use Zotel\Wallet\Services\PrepareServiceInterface;
use Zotel\Wallet\Services\TransferServiceInterface;

/**
 * @internal
 */
final readonly class TransferQueryHandler implements TransferQueryHandlerInterface
{
    public function __construct(
        private AssistantServiceInterface $assistantService,
        private TransferServiceInterface $transferService,
        private PrepareServiceInterface $prepareService,
        private AtomicServiceInterface $atomicService
    ) {
    }

    public function apply(array $objects): array
    {
        $wallets = $this->assistantService->getWallets(
            array_map(static fn (TransferQueryInterface $query): Wallet => $query->getFrom(), $objects),
        );

        $values = array_map(
            fn (TransferQueryInterface $query) => $this->prepareService->transferLazy(
                $query->getFrom(),
                $query->getTo(),
                WalletTransfer::STATUS_TRANSFER,
                $query->getAmount(),
                $query->getMeta(),
            ),
            $objects
        );

        return $this->atomicService->blocks($wallets, fn () => $this->transferService->apply($values));
    }
}
