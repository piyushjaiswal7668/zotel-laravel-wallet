<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Exceptions;

use LogicException;

final class TransactionFailedException extends LogicException implements LogicExceptionInterface
{
}
