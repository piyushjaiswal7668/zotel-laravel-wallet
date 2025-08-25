<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Assembler;

use Zotel\Wallet\External\Contracts\ExtraDtoInterface;

interface ExtraDtoAssemblerInterface
{
    /**
     * Create ExtraDto.
     *
     * @param ExtraDtoInterface|array<mixed>|null $data
     *     The data to create ExtraDto from. Can be either ExtraDtoInterface object, array or null.
     * @return ExtraDtoInterface
     *     The created ExtraDto.
     */
    public function create(ExtraDtoInterface|array|null $data): ExtraDtoInterface;
}
