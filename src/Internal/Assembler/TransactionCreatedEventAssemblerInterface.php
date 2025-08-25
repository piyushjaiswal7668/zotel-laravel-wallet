<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Zotel\Wallet\Models\WalletTransaction;

interface TransactionCreatedEventAssemblerInterface
{
    /**
     * Creates a new instance of the TransactionCreatedEventInterface from the given WalletTransaction model.
     *
     * @param WalletTransaction $transaction The transaction model to create the event from.
     * @return TransactionCreatedEventInterface The created event.
     */
    public function create(WalletTransaction $transaction): TransactionCreatedEventInterface;
}
