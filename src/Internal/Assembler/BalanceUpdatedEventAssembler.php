<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\BalanceUpdatedEvent;
use Zotel\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Models\Wallet;

final readonly class BalanceUpdatedEventAssembler implements BalanceUpdatedEventAssemblerInterface
{
    public function __construct(
        private ClockServiceInterface $clockService
    ) {
    }

    public function create(Wallet $wallet): BalanceUpdatedEventInterface
    {
        return new BalanceUpdatedEvent(
            $wallet->getKey(),
            $wallet->uuid,
            $wallet->getOriginalBalanceAttribute(),
            $this->clockService->now()
        );
    }
}
