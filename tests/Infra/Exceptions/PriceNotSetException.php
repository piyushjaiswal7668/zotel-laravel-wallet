<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Exceptions;

use Zotel\Wallet\Internal\Exceptions\InvalidArgumentExceptionInterface;
use InvalidArgumentException;

final class PriceNotSetException extends InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}
