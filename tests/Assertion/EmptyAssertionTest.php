<?php

namespace Zenstruck\Assert\Tests\Assertion;

use Zenstruck\Assert\Tests\Fixture\CountableObject;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class EmptyAssertionTest extends EvaluableAssertionTest
{
    public static function successProvider(): iterable
    {
        yield [false];
        yield [0];
        yield [null];
        yield [''];
        yield [[]];
        yield [new CountableObject(0)];
        yield [IterableObject::withCount(0)];
        yield [new \EmptyIterator()];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected "(true)" to be empty.', true];
        yield ['Expected "1" to be empty.', 1];
        yield ['Expected "foo" to be empty.', 'foo'];
        yield ['Expected "(array)" to be empty but its count is 1.', ['foo']];
        yield [\sprintf('Expected "%s" to be empty but its count is 2.', CountableObject::class), new CountableObject(2)];
        yield ['custom failure for 1 with value', 1, 'custom failure for {actual} with {custom}', ['custom' => 'value']];
        yield [\sprintf('Expected "%s" to be empty but its count is 2.', IterableObject::class), IterableObject::withCount(2)];
    }

    public static function notSuccessProvider(): iterable
    {
        yield [true];
        yield [1];
        yield ['foo'];
        yield [['foo']];
        yield [new CountableObject(2)];
        yield [IterableObject::withCount(2)];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected "(false)" to not be empty.', false];
        yield ['Expected "0" to not be empty.', 0];
        yield ['Expected "" to not be empty.', ''];
        yield ['Expected "(null)" to not be empty.', null];
        yield ['Expected "(array)" to not be empty.', []];
        yield [\sprintf('Expected "%s" to not be empty.', CountableObject::class), new CountableObject(0)];
        yield [\sprintf('Expected "%s" to not be empty.', IterableObject::class), IterableObject::withCount(0)];
        yield ['custom NOT failure for 0 with value', 0, 'custom NOT failure for {actual} with {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'isEmpty';
    }

    protected function notAssertMethod(): string
    {
        return 'isNotEmpty';
    }
}
