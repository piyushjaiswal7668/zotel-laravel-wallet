<?php

declare(strict_types=1);

namespace Zotel\Wallet\External\Api;

use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use App\Models\WalletTransaction;

/**
 * @api
 */
interface TransactionQueryHandlerInterface
{
    /**
     * High performance is achieved by inserting in batches, and there is also no check for the balance of the wallet.
     * If there is a need to check the balance, then you need to wrap the method call in the AtomicServiceInterface
     * and check the correctness of the balance manually.
     *
     * @param non-empty-array<TransactionQueryInterface> $objects
     * @return non-empty-array<string, WalletTransaction>
     *
     * @throws ExceptionInterface
     */
    public function apply(array $objects): array;
}
