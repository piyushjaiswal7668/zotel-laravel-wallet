<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Interfaces\Customer;
use Zotel\Wallet\Internal\Dto\BasketDtoInterface;
use Zotel\Wallet\Internal\Repository\WalletRepositoryInterface;
use Zotel\Wallet\Models\Wallet;

/**
 * @internal
 */
final readonly class EagerLoaderService implements EagerLoaderServiceInterface
{
    public function __construct(
        private CastServiceInterface $castService,
        private WalletRepositoryInterface $walletRepository
    ) {
    }

    public function loadWalletsByBasket(Customer $customer, BasketDtoInterface $basketDto): void
    {
        $products = [];
        /** @var array<array-key, array<array-key, int|string>> $productGroupIds */
        $productGroupIds = [];
        foreach ($basketDto->items() as $index => $item) {
            // If the wallet is installed, then there is no need for lazy loading
            if ($item->getReceiving() instanceof \Zotel\Wallet\Interfaces\Wallet) {
                continue;
            }

            $model = $this->castService->getModel($item->getProduct());
            if (! $model->relationLoaded('wallet')) {
                $products[$index] = $item->getProduct();
                $productGroupIds[$model->getMorphClass()][$index] = $model->getKey();
            }
        }

        foreach ($productGroupIds as $holderType => $holderIds) {
            $allWallets = $this->walletRepository->findDefaultAll($holderType, array_unique($holderIds));
            $wallets = [];
            foreach ($allWallets as $wallet) {
                $wallets[$wallet->holder_id] = $wallet;
            }

            foreach ($holderIds as $index => $holderId) {
                $wallet = $wallets[$holderId] ?? null;
                if ($wallet instanceof Wallet) {
                    $model = $this->castService->getModel($products[$index]);
                    $model->setRelation('wallet', $wallet);
                }
            }
        }
    }
}
