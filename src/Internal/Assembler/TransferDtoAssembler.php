<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Dto\TransferDto;
use Zotel\Wallet\Internal\Dto\TransferDtoInterface;
use Zotel\Wallet\Internal\Service\ClockServiceInterface;
use Zotel\Wallet\Internal\Service\IdentifierFactoryServiceInterface;
use Illuminate\Database\Eloquent\Model;

final readonly class TransferDtoAssembler implements TransferDtoAssemblerInterface
{
    public function __construct(
        private IdentifierFactoryServiceInterface $identifierFactoryService,
        private ClockServiceInterface $clockService,
    ) {
    }

    /**
     * @param array<mixed>|null $extra
     */
    public function create(
        int $depositId,
        int $withdrawId,
        string $status,
        Model $fromModel,
        Model $toModel,
        int $discount,
        string $fee,
        ?string $uuid,
        ?array $extra,
    ): TransferDtoInterface {
        return new TransferDto(
            $uuid ?? $this->identifierFactoryService->generate(),
            $depositId,
            $withdrawId,
            $status,
            $fromModel->getKey(),
            $toModel->getKey(),
            $discount,
            $fee,
            $extra,
            $this->clockService->now(),
            $this->clockService->now(),
        );
    }
}
