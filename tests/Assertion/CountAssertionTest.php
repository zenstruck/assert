<?php

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\Assertion\CountAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CountAssertionTest extends TestCase
{
    /**
     * @test
     */
    public function haystack_must_be_countable(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$haystack must be countable or iterable, "int" given.');

        new CountAssertion(1, 5);
    }
}
