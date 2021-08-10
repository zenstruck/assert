<?php

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\Assertion\ContainsAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ContainsAssertionTest extends TestCase
{
    /**
     * @test
     */
    public function haystack_must_be_iterable_or_scalar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('$haystack must be iterable or scalar, "stdClass" given.');

        new ContainsAssertion('foo', new \stdClass());
    }

    /**
     * @test
     */
    public function if_haystack_is_scalar_needle_must_be_scalar(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('When $haystack is scalar, $needle must also be scalar, "array" given.');

        new ContainsAssertion(['foo'], 'foo');
    }
}
