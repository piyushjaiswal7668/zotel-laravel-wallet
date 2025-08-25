<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Listeners;

use Zotel\Wallet\Internal\Events\BalanceUpdatedEventInterface;
use Zotel\Wallet\Test\Infra\Exceptions\UnknownEventException;

final class BalanceUpdatedThrowUuidListener
{
    public function handle(BalanceUpdatedEventInterface $balanceChangedEvent): never
    {
        throw new UnknownEventException(
            $balanceChangedEvent->getWalletUuid(),
            ((int) $balanceChangedEvent->getBalance()) + $balanceChangedEvent->getWalletId(),
        );
    }
}
