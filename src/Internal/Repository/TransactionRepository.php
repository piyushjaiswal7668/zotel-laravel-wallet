<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Repository;

use Zotel\Wallet\Internal\Dto\TransactionDtoInterface;
use Zotel\Wallet\Internal\Query\TransactionQueryInterface;
use Zotel\Wallet\Internal\Service\JsonServiceInterface;
use Zotel\Wallet\Internal\Transform\TransactionDtoTransformerInterface;
use Zotel\Wallet\Models\WalletTransaction;

final readonly class TransactionRepository implements TransactionRepositoryInterface
{
    public function __construct(
        private TransactionDtoTransformerInterface $transformer,
        private JsonServiceInterface $jsonService,
        private WalletTransaction $transaction
    ) {
    }

    /**
     * @param non-empty-array<int|string, TransactionDtoInterface> $objects
     */
    public function insert(array $objects): void
    {
        $values = [];
        foreach ($objects as $object) {
            $values[] = array_map(
                fn ($value) => is_array($value) ? $this->jsonService->encode($value) : $value,
                $this->transformer->extract($object)
            );
        }

        $this->transaction->newQuery()
            ->insert($values);
    }

    public function insertOne(TransactionDtoInterface $dto): WalletTransaction
    {
        $attributes = $this->transformer->extract($dto);
        $instance = $this->transaction->newInstance($attributes);
        $instance->saveQuietly();

        return $instance;
    }

    /**
     * @return WalletTransaction[]
     */
    public function findBy(TransactionQueryInterface $query): array
    {
        return $this->transaction->newQuery()
            ->whereIn('uuid', $query->getUuids())
            ->get()
            ->all();
    }
}
