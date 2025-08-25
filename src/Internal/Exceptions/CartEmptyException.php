<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Exceptions;

use InvalidArgumentException;

final class CartEmptyException extends InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}
