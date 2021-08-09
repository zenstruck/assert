<?php

namespace Zenstruck\Assert\Tests\Assertion;

use Zenstruck\Assert\Assertion\CountAssertion;
use Zenstruck\Assert\Tests\Fixture\CountableObject;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CountAssertionTest extends EvaluableAssertionTest
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

    public static function successProvider(): iterable
    {
        yield [0, []];
        yield [1, ['foo']];
        yield [0, new \EmptyIterator()];
        yield [1, new CountableObject(1)];
        yield [3, IterableObject::withCount(3)];
    }

    public static function failureProvider(): iterable
    {
        yield ['Expected the count of (array) to be 1 but got 0.', 1, []];
        yield ['Expected the count of (array) to be 1 but got 2.', 1, [1, 2]];
        yield ['Expected the count of EmptyIterator to be 1 but got 0.', 1, new \EmptyIterator()];
        yield [\sprintf('Expected the count of %s to be 1 but got 3.', CountableObject::class), 1, new CountableObject(3)];
        yield [\sprintf('Expected the count of %s to be 1 but got 2.', IterableObject::class), 1, IterableObject::withCount(2)];
        yield ['fail 1 2 (array) value', 1, [1, 2], 'fail {expected} {actual} {haystack} {custom}', ['custom' => 'value']];
    }

    public static function notSuccessProvider(): iterable
    {
        yield [0, [1]];
        yield [1, ['foo', 'bar']];
        yield [2, new \EmptyIterator()];
        yield [3, new CountableObject(1)];
        yield [2, IterableObject::withCount(3)];
    }

    public static function notFailureProvider(): iterable
    {
        yield ['Expected the count of (array) to not be 0.', 0, []];
        yield ['Expected the count of (array) to not be 2.', 2, [1, 2]];
        yield ['Expected the count of EmptyIterator to not be 0.', 0, new \EmptyIterator()];
        yield [\sprintf('Expected the count of %s to not be 3.', CountableObject::class), 3, new CountableObject(3)];
        yield [\sprintf('Expected the count of %s to not be 2.', IterableObject::class), 2, IterableObject::withCount(2)];
        yield ['fail 2 2 (array) value', 2, [1, 2], 'fail {expected} {actual} {haystack} {custom}', ['custom' => 'value']];
    }

    protected function assertMethod(): string
    {
        return 'count';
    }

    protected function notAssertMethod(): string
    {
        return 'notCount';
    }
}
