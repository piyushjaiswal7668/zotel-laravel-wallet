<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\Internal\Query\TransactionQueryInterface;

interface TransactionQueryAssemblerInterface
{
    /**
     * Creates a new transaction query from the given uuids.
     *
     * @param non-empty-array<int|string, string> $uuids The uuids of the transactions.
     * @return TransactionQueryInterface The transaction query.
     */
    public function create(array $uuids): TransactionQueryInterface;
}
