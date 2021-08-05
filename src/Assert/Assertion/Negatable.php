<?php

namespace Zenstruck\Assert\Assertion;

use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Not;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
interface Negatable
{
    public function __invoke(): void;

    /**
     * The failure to use if assertion passed but negated {@see Not}.
     */
    public function notFailure(): AssertionFailed;
}
