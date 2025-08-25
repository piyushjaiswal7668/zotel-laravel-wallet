<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Internal\Exceptions\TransactionFailedException;
use Zotel\Wallet\Internal\Service\DatabaseServiceInterface;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class DatabaseTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testCheckCode(): void
    {
        $this->expectException(TransactionFailedException::class);
        $this->expectExceptionCode(ExceptionInterface::TRANSACTION_FAILED);
        $this->expectExceptionMessage('WalletTransaction failed. Message: hello');

        app(DatabaseServiceInterface::class)->transaction(static function (): never {
            throw new \RuntimeException('hello');
        });
    }
}
