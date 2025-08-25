<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Repository;

use Zotel\Wallet\Internal\Dto\TransactionDtoInterface;
use Zotel\Wallet\Internal\Query\TransactionQueryInterface;
use App\Models\WalletTransaction;

interface TransactionRepositoryInterface
{
    /**
     * Inserts multiple transactions into the repository.
     *
     * @param non-empty-array<int|string, TransactionDtoInterface> $objects The array of transaction objects to insert.
     */
    public function insert(array $objects): void;

    /**
     * Inserts a single transaction into the repository.
     *
     * @param TransactionDtoInterface $dto The transaction object to insert.
     * @return WalletTransaction The inserted transaction object.
     */
    public function insertOne(TransactionDtoInterface $dto): WalletTransaction;

    /**
     * Retrieves transactions from the repository based on the given query.
     *
     * @param TransactionQueryInterface $query The query to filter the transactions.
     * @return WalletTransaction[] An array of transactions that match the query.
     */
    public function findBy(TransactionQueryInterface $query): array;
}
