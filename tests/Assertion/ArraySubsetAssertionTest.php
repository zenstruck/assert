<?php

declare(strict_types=1);

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Assert\Tests\Assertion;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Assertion\ArraySubsetAssertion;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

class ArraySubsetAssertionTest extends TestCase
{
    /**
     * @dataProvider arraySubsetProvider
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
        yield 'unordered keys is a subset' => [['bar' => 'foo', 'foo' => 'bar'], ['foo' => 'bar', 'bar' => 'foo']];
        yield 'subset as a list' => [[1, 2], [1, 2, 3]];
        yield 'subset as a list: order does not matter' => [[3, 1], [1, 2, 3]];
        yield 'subset as a list: with associative arrays' => [
            [['foo' => 0], ['bar' => 2]],
            [['foo' => 0, 'bar' => 0], ['foo' => 1, 'bar' => 1], ['foo' => 2, 'bar' => 2]],
        ];
        yield 'subset as a list: deep lists unordered' => [
            [['c', 'b'], 4, 1],
            [1, ['a', 'b', 'c'], 3, 4],
        ];
        yield 'deep match' => [
            ['foo' => ['foo2' => ['foo3' => 'bar', 'list' => [2]]]],
            ['foo' => ['foo2' => ['foo3' => 'bar', 'bar' => 'bar', 'list' => [1, 2, 3]], 'bar' => 'bar'], 'bar' => 'foo'],
        ];
        yield 'deep match with lists' => [
            [
                'users' => [
                    ['name' => 'name1', 'age' => 25, 'friends' => ['name3']],
                    ['name' => 'name3'],
                ],
            ],
            [
                'users' => [
                    ['name' => 'name1', 'age' => 25, 'friends' => ['name2', 'name3']],
                    ['name' => 'name2', 'age' => 26],
                    ['name' => 'name3', 'age' => 27, 'friends' => ['name1', 'name2']],
                ],
            ],
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
        yield 'assoc array match list value' => [['foo', 'bar', 'baz'], ['a' => 'foo', 'b' => 'bar', 'c' => 'baz']];

        $deepArrayWithLists = [
            'users' => [
                ['name' => 'name1', 'age' => 25, 'friends' => ['name2', 'name3']],
                ['name' => 'name2', 'age' => 26],
                ['name' => 'name3', 'age' => 27],
            ],
        ];

        yield 'assoc array in list: value fails' => [
            ['users' => [['name' => 'foo']]],
            $deepArrayWithLists,
        ];

        yield 'assoc array in list: key fails' => [
            ['users' => [['foo' => 'name1']]],
            $deepArrayWithLists,
        ];

        yield 'assoc array in list: second key fails' => [
            ['users' => [['name' => 'name1', 'foo' => 'bar']]],
            $deepArrayWithLists,
        ];

        yield 'assoc array in list: second value fails' => [
            ['users' => [['name' => 'name1', 'age' => 0]]],
            $deepArrayWithLists,
        ];

        yield 'assoc array in list: nested list invalid' => [
            ['users' => [['name' => 'name1', 'age' => 25, 'friends' => ['name1']]]],
            $deepArrayWithLists,
        ];

        yield 'assoc array in list: expected nested list is a scalar' => [
            ['users' => [['name' => 'name1', 'age' => 25, 'friends' => 'name1']]],
            $deepArrayWithLists,
        ];
    }

    /**
     * @test
     */
    public function it_throws_if_given_needle_is_not_valid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given string as needle is not a valid json list/object.');

        ArraySubsetAssertion::isSubsetOf('invalid json', []);
    }

    /**
     * @test
     */
    public function it_throws_if_given_haystack_is_not_valid_json(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Given string as haystack is not a valid json list/object.');

        ArraySubsetAssertion::isSubsetOf([], 'invalid json');
    }

    /**
     * @test
     * @dataProvider checkDiffOnFailureProvider
     *
     * @param string|iterable $needle
     * @param string|iterable $haystack
     */
    public function it_displays_the_right_array_diff_on_failure($needle, $haystack, string $expectedMessage): void
    {
        try {
            ArraySubsetAssertion::isSubsetOf($needle, $haystack)();
        } catch (AssertionFailed $e) {
        }

        Assert::that($e ?? null)->isNotNull();
        Assert::that($e->getMessage())->contains($expectedMessage);
    }

    public function checkDiffOnFailureProvider(): iterable
    {
        yield 'comparison between arrays' => [
            ['foo' => 'bar'],
            [],
            <<<FAILURE
                Expected needle to be a subset of haystack.
                Expected:
                [
                    'foo' => 'bar',
                ]

                Actual:
                []
                FAILURE,
        ];

        yield 'comparison between jsons' => [
            '{"foo":"bar"}',
            '{"bar":"foo"}',
            <<<FAILURE
                Expected needle to be a subset of haystack.
                Expected:
                {
                    "foo": "bar"
                }

                Actual:
                {
                    "bar": "foo"
                }
                FAILURE,
        ];
    }
}
