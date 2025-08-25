<?php

declare(strict_types=1);

namespace Zotel\Wallet\External\Api;

use Zotel\Wallet\External\Contracts\ExtraDtoInterface;
use Zotel\Wallet\Interfaces\Wallet;

interface TransferQueryInterface
{
    public function getFrom(): Wallet;

    public function getTo(): Wallet;

    public function getAmount(): float|int|string;

    /**
     * @return array<mixed>|ExtraDtoInterface|null
     */
    public function getMeta(): array|ExtraDtoInterface|null;
}
