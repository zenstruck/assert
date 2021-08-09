<?php

namespace Zenstruck\Assert\Tests\Assertion;

use Zenstruck\Assert\Tests\Fixture\CountableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class SameAssertionTest extends EvaluableAssertionTest
{
    public static function successProvider(): iterable
    {
        yield [5, 5];
        yield ['foo', 'foo'];
        yield [null, null];
        yield [['foo'], ['foo']];
        yield [$o = new CountableObject(4), $o];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected "5" to be the same as "6".', 5, 6];
        yield ['Expected "(null)" to be the same as "6".', null, 6];
        yield ['Expected "(int) 5" to be the same as "(string) 5".', 5, '5'];
        yield ['Expected "foo" to be the same as "bar".', 'foo', 'bar'];
        yield ['Expected "(array)" to be the same as "(array)".', ['foo'], ['bar']];
        yield ['Expected "foo" to be the same as "(array)".', 'foo', ['bar']];
        yield ['Expected "stdClass" to be the same as "stdClass".', new \stdClass(), new \stdClass()];
        yield ['fail foo bar value', 'foo', 'bar', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    public static function notSuccessProvider(): iterable
    {
        yield [5, 6];
        yield [5, '5'];
        yield ['foo', 'bar'];
        yield [['foo'], ['bar']];
        yield [new CountableObject(4), new CountableObject(4)];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected "5" to not be the same as "5".', 5, 5];
        yield ['Expected "(null)" to not be the same as "(null)".', null, null];
        yield ['Expected "foo" to not be the same as "foo".', 'foo', 'foo'];
        yield ['Expected "(array)" to not be the same as "(array)".', ['foo'], ['foo']];
        yield ['Expected "stdClass" to not be the same as "stdClass".', $o = new \stdClass(), $o];
        yield ['fail foo foo value', 'foo', 'foo', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'same';
    }

    protected function notAssertMethod(): string
    {
        return 'notSame';
    }
}
