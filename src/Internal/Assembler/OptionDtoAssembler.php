<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\External\Contracts\OptionDtoInterface;
use Zotel\Wallet\External\Dto\Option;

final class OptionDtoAssembler implements OptionDtoAssemblerInterface
{
    public function create(array|null $data): OptionDtoInterface
    {
        return new Option($data);
    }
}
