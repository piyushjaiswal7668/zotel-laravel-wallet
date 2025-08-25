<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\Factories;

use Zotel\Wallet\Test\Infra\Models\UserDynamic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<UserDynamic>
 */
final class UserDynamicFactory extends Factory
{
    protected $model = UserDynamic::class;

    public function definition(): array
    {
        return [
            'name' => fake()
                ->name,
            'email' => fake()
                ->unique()
                ->safeEmail,
        ];
    }
}
