<?php

declare(strict_types=1);

namespace Bavix\Wallet\Internal\Assembler;

use Bavix\Wallet\Internal\Events\TransactionCreatedEventInterface;
use App\Models\WalletTransaction;

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
