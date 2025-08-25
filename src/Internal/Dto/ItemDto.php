<?php

declare(strict_types=1);

namespace Zotel\Wallet\Internal\Dto;

use Zotel\Wallet\Interfaces\ProductInterface;
use Zotel\Wallet\Interfaces\Wallet;

/** @immutable */
final readonly class ItemDto implements ItemDtoInterface
{
    public function __construct(
        private ProductInterface $product,
        private int $quantity,
        private int|string|null $pricePerItem,
        private ?Wallet $receiving,
    ) {
    }

    /**
     * @return ProductInterface[]
     */
    public function getItems(): array
    {
        return array_fill(0, $this->quantity, $this->product);
    }

    public function getPricePerItem(): int|string|null
    {
        return $this->pricePerItem;
    }

    public function getProduct(): ProductInterface
    {
        return $this->product;
    }

    public function count(): int
    {
        return $this->quantity;
    }

    public function getReceiving(): ?Wallet
    {
        return $this->receiving;
    }
}
