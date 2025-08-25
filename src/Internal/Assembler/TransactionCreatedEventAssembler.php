<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\TransactionCreatedEvent;
use Zotel\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Models\WalletTransaction;

final readonly class TransactionCreatedEventAssembler implements TransactionCreatedEventAssemblerInterface
{
    public function __construct(
        private ClockServiceInterface $clockService
    ) {
    }

    public function create(WalletTransaction $transaction): TransactionCreatedEventInterface
    {
        return new TransactionCreatedEvent(
            $transaction->getKey(),
            $transaction->type,
            $transaction->wallet_id,
            $this->clockService->now(),
        );
    }
}
