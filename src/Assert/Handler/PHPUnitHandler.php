<?php

namespace Zenstruck\Assert\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use Zenstruck\Assert\Exception\AssertionFailed;
use Zenstruck\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PHPUnitHandler implements Handler
{
    public function onSuccess(): void
    {
        // trigger a successful PHPUnit assertion to avoid "risky" tests
        PHPUnit::assertTrue(true);
    }

    public function onFailure(AssertionFailed $exception): void
    {
        PHPUnit::fail($exception->getMessage());
    }

    public static function supported(): bool
    {
        return \class_exists(PHPUnit::class);
    }
}
