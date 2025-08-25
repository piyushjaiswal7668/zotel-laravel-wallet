<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Listeners;

use Zotel\Wallet\Internal\Events\TransactionCreatedEventInterface;
use Zotel\Wallet\Test\Infra\Exceptions\UnknownEventException;

final class TransactionCreatedThrowListener
{
    public function handle(TransactionCreatedEventInterface $transactionCreatedEvent): never
    {
        $type = $transactionCreatedEvent->getType();
        $createdAt = $transactionCreatedEvent->getCreatedAt()
            ->format(\DateTimeInterface::ATOM);

        $message = hash('sha256', $type.$createdAt);

        throw new UnknownEventException($message, $transactionCreatedEvent->getId());
    }
}
