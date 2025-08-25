<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\TransactionFailedException;
use Zotel\Wallet\Internal\Service\DatabaseServiceInterface;
use Zotel\Wallet\Internal\Service\LockServiceInterface;
use Zotel\Wallet\Internal\Service\StateServiceInterface;
use Illuminate\Database\RecordsNotFoundException;

/**
 * @internal
 */
final readonly class AtomicService implements AtomicServiceInterface
{
    public function __construct(
        private BookkeeperServiceInterface $bookkeeperService,
        private DatabaseServiceInterface $databaseService,
        private StateServiceInterface $stateService,
        private LockServiceInterface $lockService,
        private CastServiceInterface $castService
    ) {
    }

    /**
     * @param non-empty-array<Wallet> $objects
     *
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function blocks(array $objects, callable $callback): mixed
    {
        /** @var array<string, \Zotel\Wallet\Models\Wallet> $blockObjects */
        $blockObjects = [];
        foreach ($objects as $object) {
            $wallet = $this->castService->getWallet($object);
            if (! $this->lockService->isBlocked($wallet->uuid)) {
                $blockObjects[$wallet->uuid] = $wallet;
            }
        }

        if ($blockObjects === []) {
            return $callback();
        }

        $callable = function () use ($blockObjects, $callback) {
            $this->stateService->multiFork(
                array_keys($blockObjects),
                fn () => $this->bookkeeperService->multiAmount($blockObjects)
            );

            return $this->databaseService->transaction($callback);
        };

        try {
            return $this->lockService->blocks(array_keys($blockObjects), $callable);
        } finally {
            foreach (array_keys($blockObjects) as $uuid) {
                $this->stateService->drop($uuid);
            }
        }
    }

    /**
     * @throws RecordsNotFoundException
     * @throws TransactionFailedException
     * @throws ExceptionInterface
     */
    public function block(Wallet $object, callable $callback): mixed
    {
        return $this->blocks([$object], $callback);
    }
}
