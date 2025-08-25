<?php

declare(strict_types=1);

namespace Zotel\Wallet\Interfaces;

use Zotel\Wallet\Exceptions\BalanceIsEmpty;
use Zotel\Wallet\Exceptions\InsufficientFunds;
use Zotel\Wallet\External\Contracts\ExtraDtoInterface;
use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\RecordNotFoundException;
use Zotel\Wallet\Internal\Exceptions\TransactionFailedException;
use App\Models\WalletTransfer;
use Illuminate\Database\RecordsNotFoundException;

interface Exchangeable
{
    /**
     * Exchange currency from this wallet to another wallet.
     *
     * @param Wallet $to The wallet to exchange the currency to.
     * @param int|non-empty-string $amount The amount to exchange.
     * @param ExtraDtoInterface|array<mixed>|null $meta The extra data for the transaction.
     * @return WalletTransfer The created transfer.
     *
     * @throws BalanceIsEmpty             if the wallet does not have enough funds to make the exchange.
     * @throws InsufficientFunds          if the wallet does not have enough funds to make the exchange.
     * @throws RecordNotFoundException    if the wallet does not exist.
     * @throws RecordsNotFoundException   if the wallet does not exist.
     * @throws TransactionFailedException if the transaction fails.
     * @throws ExceptionInterface         if an unexpected error occurs.
     */
    public function exchange(Wallet $to, int|string $amount, ExtraDtoInterface|array|null $meta = null): WalletTransfer;

    /**
     * Safely exchanges currency from this wallet to another wallet.
     *
     * If an error occurs during the process, null is returned.
     *
     * @param Wallet $to The wallet to exchange the currency to.
     * @param int|non-empty-string $amount The amount to exchange.
     * @param ExtraDtoInterface|array<mixed>|null $meta The extra data for the transaction.
     * @return null|WalletTransfer The created transfer, or null if an error occurred.
     */
    public function safeExchange(
        Wallet $to,
        int|string $amount,
        ExtraDtoInterface|array|null $meta = null
    ): ?WalletTransfer;

    /**
     * Force exchange currency from this wallet to another wallet.
     *
     * This method will throw an exception if the exchange is not possible.
     *
     * @param Wallet $to The wallet to exchange the currency to.
     * @param int|non-empty-string $amount The amount to exchange.
     * @param ExtraDtoInterface|array<mixed>|null $meta The extra data for the transaction.
     * @return WalletTransfer The created transfer.
     *
     * @throws RecordNotFoundException If the wallet does not exist.
     * @throws RecordsNotFoundException If the wallet does not exist.
     * @throws TransactionFailedException If the transaction fails.
     * @throws ExceptionInterface If an unexpected error occurs.
     *
     * @see Exchangeable::exchange()
     */
    public function forceExchange(
        Wallet $to,
        int|string $amount,
        ExtraDtoInterface|array|null $meta = null
    ): WalletTransfer;
}
