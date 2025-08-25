<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\WalletCreatedEvent;
use Zotel\Wallet\Internal\Events\WalletCreatedEventInterface;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Models\Wallet;

final readonly class WalletCreatedEventAssembler implements WalletCreatedEventAssemblerInterface
{
    public function __construct(
        private ClockServiceInterface $clockService
    ) {
    }

    public function create(Wallet $wallet): WalletCreatedEventInterface
    {
        return new WalletCreatedEvent(
            $wallet->holder_type,
            $wallet->holder_id,
            $wallet->uuid,
            $wallet->getKey(),
            $this->clockService->now()
        );
    }
}
