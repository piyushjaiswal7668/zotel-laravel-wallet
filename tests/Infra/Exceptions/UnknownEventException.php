<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Exceptions;

use Zotel\Wallet\Internal\Exceptions\RuntimeExceptionInterface;
use RuntimeException;

final class UnknownEventException extends RuntimeException implements RuntimeExceptionInterface
{
}
