<?php

declare(strict_types=1);

namespace Zotel\Wallet\Exceptions;

use Zotel\Wallet\Internal\Exceptions\InvalidArgumentExceptionInterface;
use InvalidArgumentException;

final class UnconfirmedInvalid extends InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}
