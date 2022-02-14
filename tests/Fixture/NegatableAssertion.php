<?php

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
