<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Query\TransferQuery;
use Zotel\Wallet\Internal\Query\TransferQueryInterface;

final class TransferQueryAssembler implements TransferQueryAssemblerInterface
{
    /**
     * @param non-empty-array<int|string, string> $uuids
     */
    public function create(array $uuids): TransferQueryInterface
    {
        return new TransferQuery($uuids);
    }
}
