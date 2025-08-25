<?php

declare(strict_types=1);

namespace Zotel\Wallet\Services;

use Zotel\Wallet\Interfaces\MaximalTaxable;
use Zotel\Wallet\Interfaces\MinimalTaxable;
use Zotel\Wallet\Interfaces\Taxable;
use Zotel\Wallet\Interfaces\Wallet;
use Zotel\Wallet\Internal\Service\MathServiceInterface;

/**
 * @internal
 */
final readonly class TaxService implements TaxServiceInterface
{
    public function __construct(
        private MathServiceInterface $mathService,
        private CastServiceInterface $castService
    ) {
    }

    public function getFee(Wallet $wallet, float|int|string $amount): string
    {
        $fee = 0;
        if ($wallet instanceof Taxable) {
            $fee = $this->mathService->floor(
                $this->mathService->div(
                    $this->mathService->mul($amount, $wallet->getFeePercent(), 0),
                    100,
                    $this->castService->getWallet($wallet)
                        ->decimal_places
                )
            );
        }

        /**
         * Added minimum commission condition.
         *
         * @see https://github.com/bavix/laravel-wallet/issues/64#issuecomment-514483143
         */
        if ($wallet instanceof MinimalTaxable) {
            $minimal = $wallet->getMinimalFee();
            if ($this->mathService->compare($fee, $minimal) === -1) {
                $fee = $minimal;
            }
        }

        if ($wallet instanceof MaximalTaxable) {
            $maximal = $wallet->getMaximalFee();
            if ($this->mathService->compare($maximal, $fee) === -1) {
                $fee = $maximal;
            }
        }

        return (string) $fee;
    }
}
