<?php

declare(strict_types=1);

namespace Zotel\Wallet\Exceptions;

use Zotel\Wallet\Internal\Exceptions\InvalidArgumentExceptionInterface;
use InvalidArgumentException;

final class WalletOwnerInvalid extends InvalidArgumentException implements InvalidArgumentExceptionInterface
{
}
