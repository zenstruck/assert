<?php

namespace Zenstruck\Assert\Tests\Assertion;

use Zenstruck\Assert\Tests\Fixture\CountableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class EqualAssertionTest extends EvaluableAssertionTest
{
    public static function successProvider(): iterable
    {
        yield [5, 5];
        yield [5, '5'];
        yield [['foo'], ['foo']];
        yield [new CountableObject(3), new CountableObject(3)];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected "5" to be equal to "6".', 5, 6];
        yield ['Expected "5" to be equal to "6".', 5, '6'];
        yield ['Expected "(array)" to be equal to "(array)".', ['foo'], ['bar']];
        yield [\sprintf('Expected "%1$s" to be equal to "%1$s".', CountableObject::class), new CountableObject(3), new CountableObject(2)];
        yield ['fail foo bar value', 'foo', 'bar', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    public static function notSuccessProvider(): iterable
    {
        yield [5, 6];
        yield [5, '6'];
        yield [['foo'], ['bar']];
        yield [new CountableObject(3), new CountableObject(2)];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected "5" to not be equal to "5".', 5, 5];
        yield ['Expected "5" to not be equal to "5".', 5, '5'];
        yield ['Expected "(array)" to not be equal to "(array)".', ['foo'], ['foo']];
        yield [\sprintf('Expected "%1$s" to not be equal to "%1$s".', CountableObject::class), new CountableObject(3), new CountableObject(3)];
        yield ['fail foo foo value', 'foo', 'foo', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'equal';
    }

    protected function notAssertMethod(): string
    {
        return 'notEqual';
    }
}
