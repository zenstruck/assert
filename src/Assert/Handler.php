<?php

namespace Zenstruck\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Handler
{
    public function onSuccess(): void;

    public function onFailure(AssertionFailed $exception): void;
}
