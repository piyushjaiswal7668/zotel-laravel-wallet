<?php

declare(strict_types=1);

namespace Zotel\Wallet\External\Api;

use Zotel\Wallet\External\Contracts\ExtraDtoInterface;
use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Services\CastServiceInterface;
use Zotel\Wallet\Services\FormatterServiceInterface;

final readonly class TransferFloatQuery implements TransferQueryInterface
{
    private string $amount;

    /**
     * @param array<mixed>|ExtraDtoInterface|null $meta
     */
    public function __construct(
        private Wallet $from,
        private Wallet $to,
        float|int|string $amount,
        private array|ExtraDtoInterface|null $meta
    ) {
        $walletModel = app(CastServiceInterface::class)->getWallet($from);

        $this->amount = app(FormatterServiceInterface::class)
            ->intValue($amount, $walletModel->decimal_places);
    }

    public function getFrom(): Wallet
    {
        return $this->from;
    }

    public function getTo(): Wallet
    {
        return $this->to;
    }

    public function getAmount(): string
    {
        return $this->amount;
    }

    /**
     * @return array<mixed>|ExtraDtoInterface|null
     */
    public function getMeta(): array|ExtraDtoInterface|null
    {
        return $this->meta;
    }
}
