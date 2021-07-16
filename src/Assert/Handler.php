<?php

namespace Zenstruck\Assert;

use Zenstruck\Assert\Exception\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Handler
{
    public function onSuccess(): void;

    public function onFailure(AssertionFailed $exception): void;
}
