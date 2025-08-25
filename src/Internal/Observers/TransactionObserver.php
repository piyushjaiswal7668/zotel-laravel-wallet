<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Observers;

use Zotel\Wallet\Exceptions\UnconfirmedInvalid;
use Zotel\Wallet\Exceptions\WalletOwnerInvalid;
use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\RecordNotFoundException;
use Zotel\Wallet\Internal\Exceptions\TransactionFailedException;
use Zotel\Wallet\Models\WalletTransaction;
use Illuminate\Database\RecordsNotFoundException;

final class TransactionObserver
{
    /**
     * Handle the deleting event for the WalletTransaction model.
     *
     * This method is called when a transaction is being deleted.
     * It safely resets the confirmation of the transaction.
     *
     * @param WalletTransaction $model The transaction model being deleted.
     * @return bool Returns true if the confirmation was reset, false otherwise.
     *
     * @throws UnconfirmedInvalid If the transaction is not confirmed.
     * @throws WalletOwnerInvalid If the transaction does not belong to the wallet.
     * @throws RecordNotFoundException If the transaction was not found.
     * @throws RecordsNotFoundException If no transactions were found.
     * @throws TransactionFailedException If the transaction failed.
     * @throws ExceptionInterface If an exception occurred.
     */
    public function deleting(WalletTransaction $model): bool
    {
        // Reset the confirmation of the transaction.
        // This method removes the confirmation of the transaction only if it is already confirmed.
        // If the transaction does not belong to the wallet, a WalletOwnerInvalid exception will be thrown.
        // If the transaction was not found, a RecordNotFoundException will be thrown.
        return $model->wallet->safeResetConfirm($model);
    }
}
