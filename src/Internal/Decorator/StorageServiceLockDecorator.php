<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Decorator;

use Zotel\Wallet\Internal\Exceptions\RecordNotFoundException;
use Zotel\Wallet\Internal\Service\LockServiceInterface;
use Zotel\Wallet\Internal\Service\MathServiceInterface;
use Zotel\Wallet\Internal\Service\StateServiceInterface;
use Zotel\Wallet\Internal\Service\StorageServiceInterface;

final readonly class StorageServiceLockDecorator implements StorageServiceInterface
{
    public function __construct(
        private StorageServiceInterface $storageService,
        private StateServiceInterface $stateService,
        private LockServiceInterface $lockService,
        private MathServiceInterface $mathService
    ) {
    }

    public function flush(): bool
    {
        return $this->storageService->flush();
    }

    public function forget(string $uuid): bool
    {
        return $this->storageService->forget($uuid);
    }

    public function get(string $uuid): string
    {
        return current($this->multiGet([$uuid]));
    }

    public function sync(string $uuid, float|int|string $value): bool
    {
        return $this->multiSync([
            $uuid => $value,
        ]);
    }

    /**
     * @throws RecordNotFoundException
     */
    public function increase(string $uuid, float|int|string $value): string
    {
        return current($this->multiIncrease([
            $uuid => $value,
        ]));
    }

    public function multiGet(array $uuids): array
    {
        $missingKeys = [];
        $results = [];
        foreach ($uuids as $uuid) {
            $item = $this->stateService->get($uuid);
            if ($item === null) {
                $missingKeys[] = $uuid;

                continue;
            }

            $results[$uuid] = $item;
        }

        if ($missingKeys !== []) {
            $foundValues = $this->storageService->multiGet($missingKeys);
            foreach ($foundValues as $key => $value) {
                $results[$key] = $value;
            }
        }

        assert($results !== []);

        return $results;
    }

    public function multiSync(array $inputs): bool
    {
        return $this->storageService->multiSync($inputs);
    }

    public function multiIncrease(array $inputs): array
    {
        return $this->lockService->blocks(array_keys($inputs), function () use ($inputs): array {
            $multiGet = $this->multiGet(array_keys($inputs));
            $results = [];
            foreach ($multiGet as $uuid => $item) {
                $value = $this->mathService->add($item, $inputs[$uuid]);
                $results[$uuid] = $this->mathService->round($value);
            }

            $this->multiSync($results);

            assert($results !== []);

            return $results;
        });
    }
}
