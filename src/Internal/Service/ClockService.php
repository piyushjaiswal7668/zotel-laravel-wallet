<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Service;

use DateTimeImmutable;

final class ClockService implements ClockServiceInterface
{
    public function now(): DateTimeImmutable
    {
        return new DateTimeImmutable();
    }
}
