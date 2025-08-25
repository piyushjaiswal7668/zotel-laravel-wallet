<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Infra\PackageModels;

final class MyWallet extends \Zotel\Wallet\Models\Wallet
{
    public function helloWorld(): string
    {
        return 'hello world';
    }
}
