<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Internal\Dto\TransactionDtoInterface;
use Zotel\Wallet\Internal\Dto\TransferDtoInterface;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;

/**
 * @api
 */
interface AtmServiceInterface
{
    /**
     * This function helps to create a bunch of transaction objects.
     *
     * It takes an array of objects that implement the TransactionDtoInterface interface.
     * Each object represents a transaction and contains information such as the wallet,
     * transaction type, amount, and other details.
     *
     * The function returns an array of transaction objects. The keys are the transaction UUIDs
     * and the values are the transaction objects.
     *
     * @param non-empty-array<array-key, TransactionDtoInterface> $objects
     *      The array of objects that represent the transactions.
     * @return non-empty-array<string, WalletTransaction>
     *      An array of transaction objects. The keys are the transaction UUIDs and the values are the transaction
     *      objects.
     *
     * @throws \Zotel\Wallet\Internal\Exceptions\ModelNotFoundException
     *      If any of the objects does not have a wallet.
     */
    public function makeTransactions(array $objects): array;

    /**
     * Helps to get to create a bunch of transfer objects.
     *
     * The function takes an array of objects that implement the TransferDtoInterface interface.
     * Each object represents a transfer and contains information such as the deposit wallet,
     * withdraw wallet, amount, and other details.
     *
     * The function returns an array of transfer objects. The keys are the transfer UUIDs
     * and the values are the transfer objects.
     *
     * @param non-empty-array<array-key, TransferDtoInterface> $objects
     *      The array of objects that represent the transfers.
     * @return non-empty-array<string, WalletTransfer>
     *      An array of transfer objects. The keys are the transfer UUIDs and the values are the transfer
     *      objects.
     *
     * @throws \Zotel\Wallet\Internal\Exceptions\ModelNotFoundException
     *      If any of the objects does not have a wallet.
     */
    public function makeTransfers(array $objects): array;
}
