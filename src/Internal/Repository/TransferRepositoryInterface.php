<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Repository;

use Zotel\Wallet\Internal\Dto\TransferDtoInterface;
use Zotel\Wallet\Internal\Query\TransferQueryInterface;
use App\Models\WalletTransfer;

interface TransferRepositoryInterface
{
    /**
     * Inserts multiple transfers into the repository.
     *
     * @param non-empty-array<int|string, TransferDtoInterface> $objects The array of transfer objects to insert.
     */
    public function insert(array $objects): void;

    /**
     * Inserts a single transfer into the repository.
     *
     * @param TransferDtoInterface $dto The transfer object to insert.
     * @return WalletTransfer The inserted transfer.
     */
    public function insertOne(TransferDtoInterface $dto): WalletTransfer;

    /**
     * Retrieves transfers from the repository based on the given query.
     *
     * @param TransferQueryInterface $query The query used to filter the transfers.
     * @return WalletTransfer[] The array of transfers that match the query.
     */
    public function findBy(TransferQueryInterface $query): array;

    /**
     * Updates the status of transfers identified by their IDs.
     *
     * This method updates the status field of transfers in the repository
     * to the provided status, for all transfers whose IDs are included
     * in the provided array. The method returns the number of transfers
     * that were updated.
     *
     * @param string $status The new status to set for the specified transfers.
     * @param non-empty-array<int> $ids A non-empty array of transfer IDs to update.
     * @return int The number of transfers whose status was updated.
     */
    public function updateStatusByIds(string $status, array $ids): int;
}
