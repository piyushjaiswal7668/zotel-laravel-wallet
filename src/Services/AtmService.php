<?php

declare(strict_types=1);

namespace Bavix\Wallet\Services;

use Bavix\Wallet\Internal\Assembler\TransactionQueryAssemblerInterface;
use Bavix\Wallet\Internal\Assembler\TransferQueryAssemblerInterface;
use Bavix\Wallet\Internal\Dto\TransactionDtoInterface;
use Bavix\Wallet\Internal\Dto\TransferDtoInterface;
use Bavix\Wallet\Internal\Repository\TransactionRepositoryInterface;
use Bavix\Wallet\Internal\Repository\TransferRepositoryInterface;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;

/**
 * @internal
 */
final readonly class AtmService implements AtmServiceInterface
{
    public function __construct(
        private TransactionQueryAssemblerInterface $transactionQueryAssembler,
        private TransferQueryAssemblerInterface $transferQueryAssembler,
        private TransactionRepositoryInterface $transactionRepository,
        private TransferRepositoryInterface $transferRepository,
        private AssistantServiceInterface $assistantService
    ) {
    }

    /**
     * @param non-empty-array<array-key, TransactionDtoInterface> $objects
     * @return non-empty-array<string, WalletTransaction>
     */
    public function makeTransactions(array $objects): array
    {
        if (count($objects) === 1) {
            $items = [$this->transactionRepository->insertOne(reset($objects))];
        } else {
            $this->transactionRepository->insert($objects);
            $uuids = $this->assistantService->getUuids($objects);
            $query = $this->transactionQueryAssembler->create($uuids);
            $items = $this->transactionRepository->findBy($query);
        }

        assert($items !== []);

        $results = [];
        foreach ($items as $item) {
            $results[$item->uuid] = $item;
        }

        return $results;
    }

    /**
     * @param non-empty-array<array-key, TransferDtoInterface> $objects
     * @return non-empty-array<string, WalletTransfer>
     */
    public function makeTransfers(array $objects): array
    {
        if (count($objects) === 1) {
            $items = [$this->transferRepository->insertOne(reset($objects))];
        } else {
            $this->transferRepository->insert($objects);
            $uuids = $this->assistantService->getUuids($objects);
            $query = $this->transferQueryAssembler->create($uuids);
            $items = $this->transferRepository->findBy($query);
        }

        assert($items !== []);

        $results = [];
        foreach ($items as $item) {
            $results[$item->uuid] = $item;
        }

        return $results;
    }
}
