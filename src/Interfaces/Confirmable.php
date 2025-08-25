<?php

declare(strict_types=1);

namespace Zotel\Wallet\Interfaces;

use Zotel\Wallet\Exceptions\BalanceIsEmpty;
use Zotel\Wallet\Exceptions\ConfirmedInvalid;
use Zotel\Wallet\Exceptions\InsufficientFunds;
use Zotel\Wallet\Exceptions\UnconfirmedInvalid;
use Zotel\Wallet\Exceptions\WalletOwnerInvalid;
use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\RecordNotFoundException;
use Zotel\Wallet\Internal\Exceptions\TransactionFailedException;
use Zotel\Wallet\Models\WalletTransaction;
use Illuminate\Database\RecordsNotFoundException;

interface Confirmable
{
    /**
     * Confirm the transaction.
     *
     * This method confirms the given transaction if it is not already confirmed.
     *
     * @param WalletTransaction $transaction The transaction to confirm.
     * @return bool Returns true if the transaction was confirmed, false otherwise.
     *
     * @throws BalanceIsEmpty          If the balance is empty.
     * @throws InsufficientFunds       If there are insufficient funds.
     * @throws ConfirmedInvalid         If the transaction is already confirmed.
     * @throws WalletOwnerInvalid      If the transaction does not belong to the wallet.
     * @throws RecordNotFoundException If the transaction was not found.
     * @throws RecordsNotFoundException If no transactions were found.
     * @throws TransactionFailedException If the transaction failed.
     * @throws ExceptionInterface       If an exception occurred.
     */
    public function confirm(WalletTransaction $transaction): bool;

    /**
     * Safely confirms the transaction.
     *
     * This method attempts to confirm the given transaction. If an exception occurs during the confirmation process,
     * it will be caught and handled. If the confirmation is successful, true will be returned. If an exception occurs,
     * false will be returned.
     *
     * @param WalletTransaction $transaction The transaction to confirm.
     * @return bool Returns true if the transaction was confirmed, false otherwise.
     *
     * @throws BalanceIsEmpty          If the balance is empty.
     * @throws InsufficientFunds       If there are insufficient funds.
     * @throws ConfirmedInvalid         If the transaction is already confirmed.
     * @throws WalletOwnerInvalid      If the transaction does not belong to the wallet.
     * @throws RecordNotFoundException If the transaction was not found.
     * @throws RecordsNotFoundException If no transactions were found.
     * @throws TransactionFailedException If the transaction failed.
     * @throws ExceptionInterface       If an exception occurred.
     */
    public function safeConfirm(WalletTransaction $transaction): bool;

    /**
     * Reset the confirmation of the transaction.
     *
     * This method is used to remove the confirmation from a transaction.
     * If the transaction is already confirmed, a `ConfirmedInvalid` exception will be thrown.
     * If the transaction does not belong to the wallet, a `WalletOwnerInvalid` exception will be thrown.
     * If the transaction was not found, a `RecordNotFoundException` will be thrown.
     *
     * @param WalletTransaction $transaction The transaction to reset.
     * @return bool Returns true if the confirmation was reset, false otherwise.
     *
     * @throws UnconfirmedInvalid       If the transaction is not confirmed.
     * @throws WalletOwnerInvalid       If the transaction does not belong to the wallet.
     * @throws RecordNotFoundException  If the transaction was not found.
     * @throws RecordsNotFoundException If no transactions were found.
     * @throws TransactionFailedException If the transaction failed.
     * @throws ExceptionInterface        If an exception occurred.
     */
    public function resetConfirm(WalletTransaction $transaction): bool;

    /**
     * Safely reset the confirmation of the transaction.
     *
     * This method is used to remove the confirmation from a transaction.
     * If the transaction is already confirmed, the confirmation will be reset.
     * If the transaction does not belong to the wallet, a `WalletOwnerInvalid` exception will be thrown.
     * If the transaction was not found, a `RecordNotFoundException` will be thrown.
     *
     * @param WalletTransaction $transaction The transaction to reset.
     * @return bool Returns true if the confirmation was reset, false otherwise.
     */
    public function safeResetConfirm(WalletTransaction $transaction): bool;

    /**
     * Force confirm the transaction.
     *
     * This method forces the confirmation of the given transaction even if it is already confirmed.
     * If the transaction is already confirmed, a `ConfirmedInvalid` exception will be thrown.
     * If the transaction does not belong to the wallet, a `WalletOwnerInvalid` exception will be thrown.
     * If the transaction was not found, a `RecordNotFoundException` will be thrown.
     *
     * @param WalletTransaction $transaction The transaction to confirm.
     * @return bool Returns true if the transaction was confirmed, false otherwise.
     *
     * @throws ConfirmedInvalid         If the transaction is already confirmed.
     * @throws WalletOwnerInvalid       If the transaction does not belong to the wallet.
     * @throws RecordNotFoundException If the transaction was not found.
     * @throws RecordsNotFoundException If no transactions were found.
     * @throws TransactionFailedException If the transaction failed.
     * @throws ExceptionInterface       If an exception occurred.
     */
    public function forceConfirm(WalletTransaction $transaction): bool;
}
