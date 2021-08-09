<?php

namespace Zenstruck\Assert\Tests\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class LessThanAssertionTest extends EvaluableAssertionTest
{
    public static function successProvider(): iterable
    {
        yield [3, 4];
        yield [3, '4'];
        yield ['3', '4'];
        yield [3.1, 3.6];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected "3" to be less than "2".', 3, 2];
        yield ['Expected "3" to be less than "3".', 3, 3];
        yield ['Expected "3" to be less than "2".', 3, '2'];
        yield ['Expected "3" to be less than "2".', '3', '2'];
        yield ['Expected "3.1" to be less than "3".', 3.1, 3];
        yield ['fail 3 2 value', '3', '2', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    public static function notSuccessProvider(): iterable
    {
        yield [4, 3];
        yield [3, 3];
        yield ['4', 3];
        yield ['4', '3'];
        yield [3.6, 3.1];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected "2" to not be less than "3".', 2, 3];
        yield ['Expected "2" to not be less than "3".', '2', 3];
        yield ['Expected "2" to not be less than "3".', '2', '3'];
        yield ['Expected "3" to not be less than "3.1".', 3, 3.1];
        yield ['fail 2 3 value', '2', '3', 'fail {expected} {actual} {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'lessThan';
    }

    protected function notAssertMethod(): string
    {
        return 'notLessThan';
    }
}
