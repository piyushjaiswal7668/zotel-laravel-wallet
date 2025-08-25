<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Factories;

use Zotel\Wallet\Test\Infra\Models\ItemDiscount;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ItemDiscount>
 */
final class ItemDiscountFactory extends Factory
{
    protected $model = ItemDiscount::class;

    public function definition(): array
    {
        return [
            'name' => fake()
                ->domainName,
            'price' => random_int(200, 700),
            'quantity' => random_int(10, 100),
        ];
    }
}
