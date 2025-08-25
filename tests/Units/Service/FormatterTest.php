<?php

declare(strict_types=1);

namespace Zotel\Wallet\Test\Units\Service;

use Zotel\Wallet\Internal\Exceptions\ExceptionInterface;
use Zotel\Wallet\Services\FormatterServiceInterface;
use Zotel\Wallet\Test\Infra\TestCase;

/**
 * @internal
 */
final class FormatterTest extends TestCase
{
    /**
     * @throws ExceptionInterface
     */
    public function testFloatValueDP3(): void
    {
        $result = app(FormatterServiceInterface::class)->floatValue('12345', 3);

        self::assertSame('12.345', $result);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testFloatValueDP2(): void
    {
        $result = app(FormatterServiceInterface::class)->floatValue('12345', 2);

        self::assertSame('123.45', $result);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testIntValueDP3(): void
    {
        $result = app(FormatterServiceInterface::class)->intValue('12.345', 3);

        self::assertSame('12345', $result);
    }

    /**
     * @throws ExceptionInterface
     */
    public function testIntValueDP2(): void
    {
        $result = app(FormatterServiceInterface::class)->intValue('123.45', 2);

        self::assertSame('12345', $result);
    }
}
