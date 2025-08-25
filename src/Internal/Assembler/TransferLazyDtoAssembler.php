<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Internal\Dto\TransactionDtoInterface;
use Zotel\Wallet\Internal\Dto\TransferLazyDto;
use Zotel\Wallet\Internal\Dto\TransferLazyDtoInterface;

final class TransferLazyDtoAssembler implements TransferLazyDtoAssemblerInterface
{
    /**
     * @param array<mixed>|null $extra
     */
    public function create(
        Wallet $fromWallet,
        Wallet $toWallet,
        int $discount,
        string $fee,
        TransactionDtoInterface $withdrawDto,
        TransactionDtoInterface $depositDto,
        string $status,
        ?string $uuid,
        ?array $extra,
    ): TransferLazyDtoInterface {
        return new TransferLazyDto(
            $fromWallet,
            $toWallet,
            $discount,
            $fee,
            $withdrawDto,
            $depositDto,
            $status,
            $uuid,
            $extra,
        );
    }
}
