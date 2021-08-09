<?php

namespace Zenstruck\Assert\Tests\Assertion;

use Zenstruck\Assert\Assertion\ContainsAssertion;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ContainsAssertionTest extends EvaluableAssertionTest
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

    public static function successProvider(): iterable
    {
        yield ['foo', 'foobar'];
        yield ['foo', 'foo'];
        yield [null, [null, 1]];
        yield [['foo'], [null, ['foo']]];
        yield ['foo', new IterableObject(['foo', 'bar'])];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected "foobar" to contain "baz".', 'baz', 'foobar'];
        yield ['Expected "foo" to contain "bar".', 'bar', 'foo'];
        yield ['Expected "(array)" to contain "2".', 2, [null, 1]];
        yield ['Expected "(array)" to contain "(array)".', ['bar'], [null, ['foo']]];
        yield ['Expected "EmptyIterator" to contain "foo".', 'foo', new \EmptyIterator()];
        yield [\sprintf('Expected "%s" to contain "baz".', IterableObject::class), 'baz', new IterableObject(['foo', 'bar'])];
        yield ['fail 3 (array) value', 3, [1, 2], 'fail {needle} {haystack} {custom}', ['custom' => 'value']];
    }

    public static function notSuccessProvider(): iterable
    {
        yield ['baz', 'foobar'];
        yield ['bar', 'foo'];
        yield [2, [null, 1]];
        yield [['bar'], [null, ['foo']]];
        yield ['baz', new IterableObject(['foo', 'bar'])];
        yield ['baz', new \EmptyIterator()];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected "foobar" to not contain "bar".', 'bar', 'foobar'];
        yield ['Expected "foo" to not contain "foo".', 'foo', 'foo'];
        yield ['Expected "(array)" to not contain "2".', 2, [2, 1]];
        yield ['Expected "(array)" to not contain "(array)".', ['foo'], [null, ['foo']]];
        yield [\sprintf('Expected "%s" to not contain "foo".', IterableObject::class), 'foo', new IterableObject(['foo', 'bar'])];
        yield ['fail 2 (array) value', 2, [1, 2], 'fail {needle} {haystack} {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'contains';
    }

    protected function notAssertMethod(): string
    {
        return 'notContains';
    }
}
