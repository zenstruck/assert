<?php

declare(strict_types=1);

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Assertion\ArraySubsetAssertion;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

class ArraySubsetAssertionTest extends TestCase
{
    /**
     * @dataProvider arraySubsetProvider
     *
     * @test
     *
     * @param string|iterable $needle
     * @param string|iterable $haystack
     */
    public function it_asserts_array_subset($needle, $haystack): void
    {
        Assert::run(ArraySubsetAssertion::isSubsetOf($needle, $haystack));
        Assert::run(ArraySubsetAssertion::hasSubset($haystack, $needle));
    }

    public function arraySubsetProvider(): iterable
    {
        yield 'empty contains empty' => [[], []];
        yield 'any array contains empty array' => [[], ['foo' => 'bar', 'bar' => 'foo']];
        yield 'simple match' => [['foo' => 'bar'], ['foo' => 'bar', 'bar' => 'foo']];
        yield 'even unordered keys is a subset' => [['bar' => 'foo', 'foo' => 'bar'], ['foo' => 'bar', 'bar' => 'foo']];
        yield 'subset can be a list' => [[1, 2], [1, 2, 3]];
        yield 'subset can be a deep list' => [[['foo' => 0], ['bar' => 1]], [['foo' => 0, 'bar' => 0], ['foo' => 1, 'bar' => 1], ['foo' => 2, 'bar' => 2]]];
        yield 'deep match' => [
            ['foo' => ['foo2' => ['foo3' => 'bar', 'list' => [1]]]],
            ['foo' => ['foo2' => ['foo3' => 'bar', 'bar' => 'bar', 'list' => [1, 2, 3]], 'bar' => 'bar'], 'bar' => 'foo'],
        ];
        yield 'works with ArrayObject' => [
            new \ArrayObject(['foo' => 'bar']),
            new \ArrayObject(['foo' => 'bar', 'bar' => 'foo']),
        ];
        yield 'works with any iterables' => [
            new IterableObject(['foo' => 'bar']),
            new IterableObject(['foo' => 'bar', 'bar' => 'foo']),
        ];
        yield 'works with json strings' => ['{"foo":"bar"}', '{"foo":"bar"}'];
    }

    /**
     * @dataProvider arrayNotSubsetProvider
     *
     * @test
     */
    public function it_asserts_not_array_subset(array $needle, array $haystack): void
    {
        Assert::not(ArraySubsetAssertion::isSubsetOf($needle, $haystack));
        Assert::not(ArraySubsetAssertion::hasSubset($haystack, $needle));
    }

    public function arrayNotSubsetProvider(): iterable
    {
        yield 'empty array does not contain anything' => [['foo' => 'bar'], []];
        yield 'different key does not match' => [['not foo' => 'bar'], ['foo' => 'bar']];
        yield 'different value does not match' => [['foo' => 'not bar'], ['foo' => 'bar']];
        yield 'match is strict' => [['foo' => '0'], ['foo' => 0]];
        yield 'order in list is important' => [[3, 2, 1], [1, 2, 3]];
    }

    /** @test */
    public function it_throws_if_given_needle_is_not_valid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given string as needle is not a valid json list/object.');

        ArraySubsetAssertion::isSubsetOf('invalid json', []);
    }

    /** @test */
    public function it_throws_if_given_haystack_is_not_valid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given string as haystack is not a valid json list/object.');

        ArraySubsetAssertion::isSubsetOf([], 'invalid json');
    }
}
