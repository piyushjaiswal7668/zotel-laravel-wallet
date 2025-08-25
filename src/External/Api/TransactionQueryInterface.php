<?php

declare(strict_types=1);

namespace Bavix\Wallet\External\Api;

use Bavix\Wallet\Interfaces\Wallet;
use App\Models\WalletTransaction;

interface TransactionQueryInterface
{
    public const TYPE_DEPOSIT = WalletTransaction::TYPE_DEPOSIT;

    public const TYPE_WITHDRAW = WalletTransaction::TYPE_WITHDRAW;

    /**
     * @return self::TYPE_DEPOSIT|self::TYPE_WITHDRAW
     */
    public function getType(): string;

    public function getWallet(): Wallet;

    public function getAmount(): float|int|string;

    /**
     * @return array<mixed>|null
     */
    public function getMeta(): ?array;

    public function isConfirmed(): bool;

    public function getUuid(): ?string;
}
