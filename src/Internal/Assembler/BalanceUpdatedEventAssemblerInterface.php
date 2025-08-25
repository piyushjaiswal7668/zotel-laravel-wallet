<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use App\Models\Wallet;

interface BalanceUpdatedEventAssemblerInterface
{
    /**
     * Create a balance updated event from a wallet.
     *
     * @param Wallet $wallet The wallet to create the event from.
     * @return BalanceUpdatedEventInterface The created event.
     */
    public function create(Wallet $wallet): BalanceUpdatedEventInterface;
}
