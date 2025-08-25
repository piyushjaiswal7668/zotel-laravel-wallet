<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Dto\TransactionDto;
use Zotel\Wallet\Internal\Dto\TransactionDtoInterface;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Internal\Service\IdentifierFactoryServiceInterface;
use Illuminate\Database\Eloquent\Model;

final readonly class TransactionDtoAssembler implements TransactionDtoAssemblerInterface
{
    public function __construct(
        private IdentifierFactoryServiceInterface $identifierFactoryService,
        private ClockServiceInterface $clockService,
    ) {
    }

    public function create(
        Model $payable,
        int $walletId,
        string $type,
        float|int|string $amount,
        bool $confirmed,
        ?array $meta,
        ?string $uuid
    ): TransactionDtoInterface {
        return new TransactionDto(
            $uuid ?? $this->identifierFactoryService->generate(),
            $payable->getMorphClass(),
            $payable->getKey(),
            $walletId,
            $type,
            $amount,
            $confirmed,
            $meta,
            $this->clockService->now(),
            $this->clockService->now(),
        );
    }
}
