<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Service;

use Ramsey\Uuid\UuidFactory;

/**
 * @codeCoverageIgnore
 */
final readonly class UuidFactoryService implements UuidFactoryServiceInterface
{
    public function __construct(
        private UuidFactory $uuidFactory
    ) {
    }

    public function uuid4(): string
    {
        return $this->uuidFactory->uuid4()
            ->toString();
    }
}
