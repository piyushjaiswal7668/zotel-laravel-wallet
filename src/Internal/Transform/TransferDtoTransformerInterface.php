<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Transform;

use Zotel\Wallet\Internal\Dto\TransferDtoInterface;
use DateTimeImmutable;

interface TransferDtoTransformerInterface
{
    /**
     * @return array{
     *     uuid: non-empty-string,
     *     deposit_id: int,
     *     withdraw_id: int,
     *     status: string,
     *     from_id: int|string,
     *     to_id: int|string,
     *     discount: int,
     *     fee: non-empty-string,
     *     extra: array<mixed>|null,
     *     created_at: DateTimeImmutable,
     *     updated_at: DateTimeImmutable,
     * }
     */
    public function extract(TransferDtoInterface $dto): array;
}
