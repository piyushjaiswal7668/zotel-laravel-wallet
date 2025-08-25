<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\External\Contracts\ExtraDtoInterface;
use Zotel\Wallet\External\Dto\Extra;

final readonly class ExtraDtoAssembler implements ExtraDtoAssemblerInterface
{
    public function __construct(
        private OptionDtoAssemblerInterface $optionDtoAssembler
    ) {
    }

    public function create(ExtraDtoInterface|array|null $data): ExtraDtoInterface
    {
        if ($data instanceof ExtraDtoInterface) {
            return $data;
        }

        $option = $this->optionDtoAssembler->create($data);

        return new Extra($option, $option, null);
    }
}
