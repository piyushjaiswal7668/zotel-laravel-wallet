<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Events\WalletCreatedEventInterface;
use Zotel\Wallet\Models\Wallet;

interface WalletCreatedEventAssemblerInterface
{
    /**
     * Assemble the wallet created event.
     */
    public function create(Wallet $wallet): WalletCreatedEventInterface;
}
