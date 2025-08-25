<?php

declare(strict_types=1);

namespace Zotel\Wallet\Exceptions;

use Zotel\Wallet\Internal\Exceptions\LogicExceptionInterface;
use LogicException;

final class InsufficientFunds extends LogicException implements LogicExceptionInterface
{
}
