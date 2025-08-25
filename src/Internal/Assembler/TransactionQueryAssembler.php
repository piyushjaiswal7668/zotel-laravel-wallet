<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Query\TransactionQuery;
use Zotel\Wallet\Internal\Query\TransactionQueryInterface;

final class TransactionQueryAssembler implements TransactionQueryAssemblerInterface
{
    /**
     * @param non-empty-array<int|string, string> $uuids
     */
    public function create(array $uuids): TransactionQueryInterface
    {
        return new TransactionQuery($uuids);
    }
}
