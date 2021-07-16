<?php

namespace Zenstruck\Assert\Handler;

use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class DefaultHandler implements Handler
{
    public function onSuccess(): void
    {
        // noop
    }

    public function onFailure(AssertionFailed $exception): void
    {
        throw $exception;
    }
}
