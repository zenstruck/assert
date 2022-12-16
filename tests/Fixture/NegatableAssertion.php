<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Assert\Tests\Fixture;

use Zenstruck\Assert\Assertion\Negatable;
use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NegatableAssertion implements Negatable
{
    /** @var bool */
    private $fail;

    public function __construct(bool $fail)
    {
        $this->fail = $fail;
    }

    public function __invoke(): void
    {
        if ($this->fail) {
            throw new AssertionFailed('assertion failed');
        }
    }

    public function notFailure(): AssertionFailed
    {
        return new AssertionFailed('negation failed');
    }
}
