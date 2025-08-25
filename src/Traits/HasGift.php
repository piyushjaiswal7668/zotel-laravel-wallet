<?php

declare(strict_types=1);

namespace Bavix\Wallet\Traits;

use function app;
use Bavix\Wallet\Exceptions\BalanceIsEmpty;
use Bavix\Wallet\Exceptions\InsufficientFunds;
use Bavix\Wallet\Interfaces\ProductInterface;
use Bavix\Wallet\Interfaces\Wallet;
use Bavix\Wallet\Internal\Assembler\TransferDtoAssemblerInterface;
use Bavix\Wallet\Internal\Exceptions\ExceptionInterface;
use Bavix\Wallet\Internal\Exceptions\TransactionFailedException;
use Bavix\Wallet\Internal\Service\MathServiceInterface;
use App\Models\WalletTransaction;
use App\Models\WalletTransfer;
use Bavix\Wallet\Services\AtmServiceInterface;
use Bavix\Wallet\Services\AtomicServiceInterface;
use Bavix\Wallet\Services\CastServiceInterface;
use Bavix\Wallet\Services\ConsistencyServiceInterface;
use Bavix\Wallet\Services\DiscountServiceInterface;
use Bavix\Wallet\Services\TaxServiceInterface;
use Bavix\Wallet\Services\TransactionServiceInterface;
use Illuminate\Database\RecordsNotFoundException;

/**
 * Trait HasGift.
 *
 * @psalm-require-extends \Illuminate\Database\Eloquent\Model
 */
trait HasGift
{
    /**
     * Safely gives the goods to the specified wallet.
     *
     * This method attempts to give the goods to the specified wallet without throwing an exception.
     * If an exception occurs during the process, `null` is returned.
     *
     * @param Wallet $to The wallet to which the goods will be given.
     * @param ProductInterface $product The goods to be given.
     * @param bool $force [optional] Whether to force the gift. Defaults to `false`.
     * @return WalletTransfer|null The transfer object representing the gift, or `null` if the gift fails.
     *
     * @throws ExceptionInterface If an exception occurs during the process of giving the goods.
     */
    public function safeGift(Wallet $to, ProductInterface $product, bool $force = false): ?WalletTransfer
    {
        try {
            // Attempt to give the goods to the specified wallet
            return $this->gift($to, $product, $force);
        } catch (ExceptionInterface $exception) {
            // If an exception occurs, return null
            return null;
        }
    }

    /**
     * Give the goods to another user (wallet).
     *
     * This method attempts to give a product to another user's wallet. If the gift is successful, the method
     * returns the transfer object. If the gift fails due to a reason other than the above, it throws a more
     * specific exception.
     *
     * @param Wallet $to The wallet to which the goods will be given.
     * @param ProductInterface $product The goods to be given.
     * @param bool $force [optional] Whether to force the gift. Defaults to `false`.
     * @return WalletTransfer The transfer object representing the gift.
     *
     * @throws BalanceIsEmpty If the balance of the wallet is empty.
     * @throws InsufficientFunds If there are insufficient funds in the wallet.
     * @throws RecordsNotFoundException If the record is not found.
     * @throws TransactionFailedException If the transaction fails.
     * @throws ExceptionInterface If an exception occurs.
     */
    public function gift(Wallet $to, ProductInterface $product, bool $force = false): WalletTransfer
    {
        // Execute the gift operation atomically
        $atomicService = app(AtomicServiceInterface::class);
        $mathService = app(MathServiceInterface::class);
        $discountService = app(DiscountServiceInterface::class);
        $taxService = app(TaxServiceInterface::class);
        $transactionService = app(TransactionServiceInterface::class);
        $castService = app(CastServiceInterface::class);
        $consistencyService = app(ConsistencyServiceInterface::class);
        $atmService = app(AtmServiceInterface::class);
        $transferDtoAssembler = app(TransferDtoAssemblerInterface::class);

        return $atomicService->block($this, function () use (
            $to,
            $product,
            $force,
            $mathService,
            $discountService,
            $taxService,
            $transactionService,
            $castService,
            $consistencyService,
            $atmService,
            $transferDtoAssembler
        ): WalletTransfer {
            // Get the discount for the product
            $discount = $discountService->getDiscount($this, $product);

            // Calculate the amount to be transferred after applying the discount
            $amount = $mathService->sub($product->getAmountProduct($this), $discount);

            // Get the fee for the transaction
            $fee = $taxService->getFee($product, $amount);

            // Check if the gift can be forced without checking the balance
            if (! $force) {
                // Check the consistency of the potential transaction
                $consistencyService->checkPotential($this, $mathService->add($amount, $fee));
            }

            // Create withdraw and deposit transactions
            $withdraw = $transactionService->makeOne(
                $this,
                WalletTransaction::TYPE_WITHDRAW,
                $mathService->add($amount, $fee),
                $product->getMetaProduct()
            );
            $deposit = $transactionService->makeOne(
                $product,
                WalletTransaction::TYPE_DEPOSIT,
                $amount,
                $product->getMetaProduct()
            );

            // Create a transfer object
            $transfer = $transferDtoAssembler->create(
                $deposit->getKey(),
                $withdraw->getKey(),
                WalletTransfer::STATUS_GIFT,
                $castService->getWallet($to),
                $castService->getWallet($product),
                $discount,
                $fee,
                null,
                null
            );

            // Create the transfer using the atm service
            $transfers = $atmService->makeTransfers([$transfer]);

            // Return the created transfer
            return current($transfers);
        });
    }

    /**
     * Force a gift without checking balance.
     *
     * This method attempts to gift a product to another user's wallet without checking the balance.
     * If the gift is successful, the method returns the transfer object. If the gift fails due
     * to a reason other than the above, it throws a more specific exception.
     *
     * @param Wallet $to The wallet to which the gift will be given.
     * @param ProductInterface $product The product to be given.
     * @return WalletTransfer The transfer object representing the gift.
     *
     * @throws RecordsNotFoundException If the record is not found.
     * @throws TransactionFailedException If the transaction fails.
     * @throws ExceptionInterface If an exception occurs.
     */
    public function forceGift(Wallet $to, ProductInterface $product): WalletTransfer
    {
        // Call the gift method with force true
        return $this->gift($to, $product, true);
    }
}
