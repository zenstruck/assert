<?php

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Tests\Fixture\CountableObject;
use Zenstruck\Assert\Tests\Fixture\IterableObject;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ExpectationTest extends TestCase
{
    use HasTraceableHandler;

    /**
     * @test
     */
    public function is_empty(): void
    {
        $this->assertSuccess(8, function() {
            Assert::that(false)->isEmpty();
            Assert::that(0)->isEmpty();
            Assert::that(null)->isEmpty();
            Assert::that('')->isEmpty();
            Assert::that([])->isEmpty();
            Assert::that(new CountableObject(0))->isEmpty();
            Assert::that(IterableObject::withCount(0))->isEmpty();
            Assert::that(new \EmptyIterator())->isEmpty();
        });

        $this
            ->assertFails('Expected "(true)" to be empty.', function() { Assert::that(true)->isEmpty(); })
            ->assertFails('Expected "1" to be empty.', function() { Assert::that(1)->isEmpty(); })
            ->assertFails('Expected "(array)" to be empty but its count is 1.', function() { Assert::that(['foo'])->isEmpty(); })
            ->assertFails(
                \sprintf('Expected "%s" to be empty but its count is 2.', CountableObject::class),
                function() { Assert::that(new CountableObject(2))->isEmpty(); }
            )
            ->assertFails(
                \sprintf('Expected "%s" to be empty but its count is 2.', IterableObject::class),
                function() { Assert::that(IterableObject::withCount(2))->isEmpty(); }
            )
            ->assertFails(
                'custom failure for 1 with value',
                function() { Assert::that(1)->isEmpty('custom failure for {actual} with {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function is_not_empty(): void
    {
        $this->assertSuccess(6, function() {
            Assert::that(true)->isNotEmpty();
            Assert::that(1)->isNotEmpty();
            Assert::that('foo')->isNotEmpty();
            Assert::that(['foo'])->isNotEmpty();
            Assert::that(new CountableObject(3))->isNotEmpty();
            Assert::that(IterableObject::withCount(1))->isNotEmpty();
        });

        $this
            ->assertFails('Expected "(false)" to not be empty.', function() { Assert::that(false)->isNotEmpty(); })
            ->assertFails('Expected "0" to not be empty.', function() { Assert::that(0)->isNotEmpty(); })
            ->assertFails('Expected "" to not be empty.', function() { Assert::that('')->isNotEmpty(); })
            ->assertFails('Expected "(null)" to not be empty.', function() { Assert::that(null)->isNotEmpty(); })
            ->assertFails('Expected "(array)" to not be empty.', function() { Assert::that([])->isNotEmpty(); })
            ->assertFails(
                \sprintf('Expected "%s" to not be empty.', CountableObject::class),
                function() { Assert::that(new CountableObject(0))->isNotEmpty(); }
            )
            ->assertFails(
                \sprintf('Expected "%s" to not be empty.', IterableObject::class),
                function() { Assert::that(IterableObject::withCount(0))->isNotEmpty(); }
            )
            ->assertFails(
                'custom NOT failure for 0 with value',
                function() { Assert::that(0)->isNotEmpty('custom NOT failure for {actual} with {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function has_count(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that([])->hasCount(0);
            Assert::that(['foo'])->hasCount(1);
            Assert::that(new \EmptyIterator())->hasCount(0);
            Assert::that(new CountableObject(1))->hasCount(1);
            Assert::that(IterableObject::withCount(3))->hasCount(3);
        });

        $this
            ->assertFails('Expected the count of (array) to be 1 but got 0.', function() { Assert::that([])->hasCount(1); })
            ->assertFails('Expected the count of (array) to be 1 but got 2.', function() { Assert::that([1, 2])->hasCount(1); })
            ->assertFails('Expected the count of EmptyIterator to be 1 but got 0.', function() { Assert::that(new \EmptyIterator())->hasCount(1); })
            ->assertFails(
                \sprintf('Expected the count of %s to be 1 but got 3.', CountableObject::class),
                function() { Assert::that(new CountableObject(3))->hasCount(1); }
            )
            ->assertFails(
                \sprintf('Expected the count of %s to be 1 but got 2.', IterableObject::class),
                function() { Assert::that(IterableObject::withCount(2))->hasCount(1); }
            )
            ->assertFails(
                'fail 1 2 (array) value',
                function() { Assert::that([1, 2])->hasCount(1, 'fail {expected} {actual} {haystack} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function does_not_have_count(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that([])->doesNotHaveCount(1);
            Assert::that(['foo'])->doesNotHaveCount(2);
            Assert::that(new \EmptyIterator())->doesNotHaveCount(1);
            Assert::that(new CountableObject(1))->doesNotHaveCount(0);
            Assert::that(IterableObject::withCount(3))->doesNotHaveCount(2);
        });

        $this
            ->assertFails('Expected the count of (array) to not be 0.', function() { Assert::that([])->doesNotHaveCount(0); })
            ->assertFails('Expected the count of (array) to not be 2.', function() { Assert::that([1, 2])->doesNotHaveCount(2); })
            ->assertFails('Expected the count of EmptyIterator to not be 0.', function() { Assert::that(new \EmptyIterator())->doesNotHaveCount(0); })
            ->assertFails(
                \sprintf('Expected the count of %s to not be 3.', CountableObject::class),
                function() { Assert::that(new CountableObject(3))->doesNotHaveCount(3); }
            )
            ->assertFails(
                \sprintf('Expected the count of %s to not be 2.', IterableObject::class),
                function() { Assert::that(IterableObject::withCount(2))->doesNotHaveCount(2); }
            )
            ->assertFails(
                'fail 2 2 (array) value',
                function() { Assert::that([1, 2])->doesNotHaveCount(2, 'fail {expected} {actual} {haystack} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function contains_assertion(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that('foobar')->contains('foo');
            Assert::that('foo')->contains('foo');
            Assert::that([null, 1])->contains(null);
            Assert::that([null, ['foo']])->contains(['foo']);
            Assert::that(new IterableObject(['foo', 'bar']))->contains('foo');
        });

        $this
            ->assertFails('Expected "foobar" to contain "baz".', function() { Assert::that('foobar')->contains('baz'); })
            ->assertFails('Expected "foo" to contain "bar".', function() { Assert::that('foo')->contains('bar'); })
            ->assertFails('Expected "(array)" to contain "2".', function() { Assert::that([null, 1])->contains(2); })
            ->assertFails('Expected "(array)" to contain "(array)".', function() { Assert::that([null, ['foo']])->contains(['bar']); })
            ->assertFails('Expected "EmptyIterator" to contain "foo".', function() { Assert::that(new \EmptyIterator())->contains('foo'); })
            ->assertFails(
                'fail 3 (array) value',
                function() { Assert::that([1, 2])->contains(3, 'fail {needle} {haystack} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function does_not_contain(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that('foobar')->doesNotContain('baz');
            Assert::that('foo')->doesNotContain('bar');
            Assert::that([null, 1])->doesNotContain(2);
            Assert::that([null, ['foo']])->doesNotContain(['bar']);
            Assert::that(new IterableObject(['foo', 'bar']))->doesNotContain('baz');
        });

        $this
            ->assertFails('Expected "foobar" to not contain "bar".', function() { Assert::that('foobar')->doesNotContain('bar'); })
            ->assertFails('Expected "foo" to not contain "foo".', function() { Assert::that('foo')->doesNotContain('foo'); })
            ->assertFails('Expected "(array)" to not contain "1".', function() { Assert::that([null, 1])->doesNotContain(1); })
            ->assertFails('Expected "(array)" to not contain "(array)".', function() { Assert::that([null, ['foo']])->doesNotContain(['foo']); })
            ->assertFails(
                'fail 2 (array) value',
                function() { Assert::that([1, 2])->doesNotContain(2, 'fail {needle} {haystack} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function equals(): void
    {
        $this->assertSuccess(7, function() {
            Assert::that(5)->equals(5);
            Assert::that(5)->equals('5');
            Assert::that('5')->equals('5');
            Assert::that('5')->equals(5);
            Assert::that(['foo'])->equals(['foo']);
            Assert::that(new CountableObject(3))->equals(new CountableObject(3));
            Assert::that([new CountableObject(3)])->equals([new CountableObject(3)]);
        });

        $this
            ->assertFails('Expected "5" to be equal to "6".', function() { Assert::that(5)->equals(6); })
            ->assertFails('Expected "5" to be equal to "6".', function() { Assert::that(5)->equals('6'); })
            ->assertFails('Expected "(array)" to be equal to "(array)".', function() { Assert::that(['foo'])->equals(['bar']); })
            ->assertFails(
                \sprintf('Expected "%1$s" to be equal to "%1$s".', CountableObject::class),
                function() { Assert::that(new CountableObject(3))->equals(new CountableObject(2)); })
            ->assertFails(
                'fail foo bar value',
                function() { Assert::that('foo')->equals('bar', 'fail {actual} {expected} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function is_not_equal_to(): void
    {
        $this->assertSuccess(7, function() {
            Assert::that(5)->isNotEqualTo(6);
            Assert::that(5)->isNotEqualTo('6');
            Assert::that('5')->isNotEqualTo('6');
            Assert::that('6')->isNotEqualTo(5);
            Assert::that(['foo'])->isNotEqualTo(['bar']);
            Assert::that(new CountableObject(2))->isNotEqualTo(new CountableObject(3));
            Assert::that([new CountableObject(3)])->isNotEqualTo([new CountableObject(2)]);
        });

        $this
            ->assertFails('Expected "5" to not be equal to "5".', function() { Assert::that(5)->isNotEqualTo(5); })
            ->assertFails('Expected "5" to not be equal to "5".', function() { Assert::that(5)->isNotEqualTo('5'); })
            ->assertFails(
                \sprintf('Expected "%1$s" to not be equal to "%1$s".', CountableObject::class),
                function() { Assert::that(new CountableObject(3))->isNotEqualTo(new CountableObject(3)); }
            )
            ->assertFails(
                'fail foo foo value',
                function() { Assert::that('foo')->isNotEqualTo('foo', 'fail {expected} {actual} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function is(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that(5)->is(5);
            Assert::that('foo')->is('foo');
            Assert::that(null)->is(null);
            Assert::that(['foo'])->is(['foo']);
            Assert::that($o = new CountableObject(4))->is($o);
        });

        $this
            ->assertFails('Expected "5" to be the same as "6".', function() { Assert::that(5)->is(6); })
            ->assertFails('Expected "6" to be the same as "(null)".', function() { Assert::that(6)->is(null); })
            ->assertFails('Expected "(string) 5" to be the same as "(int) 5".', function() { Assert::that('5')->is(5); })
            ->assertFails('Expected "foo" to be the same as "bar".', function() { Assert::that('foo')->is('bar'); })
            ->assertFails('Expected "(array)" to be the same as "(array)".', function() { Assert::that(['foo'])->is(['bar']); })
            ->assertFails('Expected "foo" to be the same as "(array)".', function() { Assert::that('foo')->is(['foo']); })
            ->assertFails('Expected "stdClass" to be the same as "ArrayIterator".', function() { Assert::that(new \stdClass())->is(new \ArrayIterator()); })
            ->assertFails('Expected "stdClass" to be the same as "stdClass".', function() { Assert::that(new \stdClass())->is(new \stdClass()); })
            ->assertFails('fail foo bar value', function() { Assert::that('foo')->is('bar', 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_not(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that(5)->isNot(6);
            Assert::that('foo')->isNot('bar');
            Assert::that(null)->isNot(1);
            Assert::that(['foo'])->isNot(['bar']);
            Assert::that(new CountableObject(4))->isNot(new CountableObject(4));
        });

        $this
            ->assertFails('Expected "5" to not be the same as "5".', function() { Assert::that(5)->isNot(5); })
            ->assertFails('Expected "(null)" to not be the same as "(null)".', function() { Assert::that(null)->isNot(null); })
            ->assertFails('Expected "foo" to not be the same as "foo".', function() { Assert::that('foo')->isNot('foo'); })
            ->assertFails('Expected "(array)" to not be the same as "(array)".', function() { Assert::that(['foo'])->isNot(['foo']); })
            ->assertFails('Expected "stdClass" to not be the same as "stdClass".', function() { Assert::that($o = new \stdClass())->isNot($o); })
            ->assertFails('fail foo foo value', function() { Assert::that('foo')->isNot('foo', 'fail {expected} {actual} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_null(): void
    {
        $this->assertSuccess(1, function() {
            Assert::that(null)->isNull();
        });

        $this
            ->assertFails('Expected "5" to be null.', function() { Assert::that(5)->isNull(); })
            ->assertFails('fail foo value', function() { Assert::that('foo')->isNull('fail {actual} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_not_null(): void
    {
        $this->assertSuccess(1, function() {
            Assert::that('foo')->isNotNull();
        });

        $this
            ->assertFails('Expected the value to not be null.', function() { Assert::that(null)->isNotNull(); })
            ->assertFails('fail value', function() { Assert::that('foo')->isNull('fail {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_greater_than(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that(3)->isGreaterThan(2);
            Assert::that('3')->isGreaterThan(2);
            Assert::that(3)->isGreaterThan('2');
            Assert::that('3')->isGreaterThan('2');
            Assert::that(2.1)->isGreaterThan(2);
        });

        $this
            ->assertFails('Expected "2" to be greater than "3".', function() { Assert::that(2)->isGreaterThan(3); })
            ->assertFails('Expected "3" to be greater than "3".', function() { Assert::that('3')->isGreaterThan(3); })
            ->assertFails('Expected "2" to be greater than "3".', function() { Assert::that('2')->isGreaterThan('3'); })
            ->assertFails('Expected "2" to be greater than "3".', function() { Assert::that(2)->isGreaterThan('3'); })
            ->assertFails('Expected "2" to be greater than "2.1".', function() { Assert::that(2)->isGreaterThan(2.1); })
            ->assertFails('fail 2 3 value', function() { Assert::that(2)->isGreaterThan(3, 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_greater_than_or_equal_to(): void
    {
        $this->assertSuccess(6, function() {
            Assert::that(3)->isGreaterThanOrEqualTo(2);
            Assert::that(3)->isGreaterThanOrEqualTo(3);
            Assert::that('3')->isGreaterThanOrEqualTo(2);
            Assert::that(3)->isGreaterThanOrEqualTo('2');
            Assert::that('3')->isGreaterThanOrEqualTo('2');
            Assert::that(2.1)->isGreaterThanOrEqualTo(2);
        });

        $this
            ->assertFails('Expected "2" to be greater than or equal to "3".', function() { Assert::that(2)->isGreaterThanOrEqualTo(3); })
            ->assertFails('Expected "2" to be greater than or equal to "3".', function() { Assert::that('2')->isGreaterThanOrEqualTo('3'); })
            ->assertFails('Expected "2" to be greater than or equal to "3".', function() { Assert::that(2)->isGreaterThanOrEqualTo('3'); })
            ->assertFails('Expected "2" to be greater than or equal to "2.1".', function() { Assert::that(2)->isGreaterThanOrEqualTo(2.1); })
            ->assertFails('fail 2 3 value', function() { Assert::that(2)->isGreaterThanOrEqualTo(3, 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_less_than(): void
    {
        $this->assertSuccess(5, function() {
            Assert::that(2)->isLessThan(3);
            Assert::that('2')->isLessThan(3);
            Assert::that(2)->isLessThan('3');
            Assert::that('2')->isLessThan('3');
            Assert::that(2)->isLessThan(2.1);
        });

        $this
            ->assertFails('Expected "2" to be less than "1".', function() { Assert::that(2)->isLessThan(1); })
            ->assertFails('Expected "3" to be less than "1".', function() { Assert::that('3')->isLessThan(1); })
            ->assertFails('Expected "2" to be less than "1".', function() { Assert::that('2')->isLessThan('1'); })
            ->assertFails('Expected "2" to be less than "1".', function() { Assert::that(2)->isLessThan('1'); })
            ->assertFails('Expected "2.1" to be less than "2".', function() { Assert::that(2.1)->isLessThan(2); })
            ->assertFails('fail 3 2 value', function() { Assert::that(3)->isLessThan(2, 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_less_than_or_equal_to(): void
    {
        $this->assertSuccess(6, function() {
            Assert::that(3)->isLessThanOrEqualTo(4);
            Assert::that(3)->isLessThanOrEqualTo(3);
            Assert::that('3')->isLessThanOrEqualTo(4);
            Assert::that(3)->isLessThanOrEqualTo('4');
            Assert::that('3')->isLessThanOrEqualTo('4');
            Assert::that(2)->isLessThanOrEqualTo(2.1);
        });

        $this
            ->assertFails('Expected "4" to be less than or equal to "3".', function() { Assert::that(4)->isLessThanOrEqualTo(3); })
            ->assertFails('Expected "4" to be less than or equal to "3".', function() { Assert::that('4')->isLessThanOrEqualTo('3'); })
            ->assertFails('Expected "4" to be less than or equal to "3".', function() { Assert::that(4)->isLessThanOrEqualTo('3'); })
            ->assertFails('Expected "2.1" to be less than or equal to "2".', function() { Assert::that(2.1)->isLessThanOrEqualTo(2); })
            ->assertFails('fail 3 2 value', function() { Assert::that(3)->isLessThanOrEqualTo(2, 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_instance_of(): void
    {
        $this->assertSuccess(2, function() {
            Assert::that($this)->isInstanceOf(__CLASS__);
            Assert::that($this)->isInstanceOf(TestCase::class);
        });

        $this
            ->assertFails(\sprintf('Expected "%s" to be an instance of "%s".', __CLASS__, Assert::class), function() { Assert::that($this)->isInstanceOf(Assert::class); })
            ->assertFails(\sprintf('Expected "(null)" to be an instance of "%s".', Assert::class), function() { Assert::that(null)->isInstanceOf(Assert::class); })
            ->assertFails(\sprintf('Expected "6" to be an instance of "%s".', Assert::class), function() { Assert::that(6)->isInstanceOf(Assert::class); })
            ->assertFails(\sprintf('Expected "%s" to be an instance of "foo".', __CLASS__), function() { Assert::that($this)->isInstanceOf('foo'); })
            ->assertFails('fail bar foo value', function() { Assert::that('bar')->isInstanceOf('foo', 'fail {actual} {expected} {custom}', ['custom' => 'value']); })
        ;
    }

    /**
     * @test
     */
    public function is_not_instance_of(): void
    {
        $this->assertSuccess(3, function() {
            Assert::that($this)->isNotInstanceOf(Assert::class);
            Assert::that(null)->isNotInstanceOf(TestCase::class);
            Assert::that(6)->isNotInstanceOf('foo');
        });

        $this
            ->assertFails(
                \sprintf('Expected "%s" to not be an instance of "%s".', __CLASS__, __CLASS__),
                function() { Assert::that($this)->isNotInstanceOf(__CLASS__); }
            )
            ->assertFails(
                \sprintf('Expected "%s" to not be an instance of "%s".', __CLASS__, TestCase::class),
                function() { Assert::that($this)->isNotInstanceOf(TestCase::class); }
            )
            ->assertFails(
                \sprintf('fail %s %s value', __CLASS__, TestCase::class),
                function() { Assert::that($this)->isNotInstanceOf(TestCase::class, 'fail {actual} {expected} {custom}', ['custom' => 'value']); }
            )
        ;
    }

    /**
     * @test
     */
    public function throws(): void
    {
        $this->assertSuccess(8, function() {
            Assert::that(function() { throw new \RuntimeException(); })->throws(\RuntimeException::class);
            Assert::that(function() { throw new \RuntimeException(); })->throws(\Exception::class);
            Assert::that(function() { throw new \RuntimeException('foo bar'); })->throws(\Exception::class, 'foo bar');
            Assert::that(function() { throw new \RuntimeException('foo bar'); })->throws(\Exception::class, 'foo');
            Assert::that(function() { throw new \RuntimeException(); })->throws(
                function(\RuntimeException $e) {}
            );
            Assert::that(function() { throw new \RuntimeException(); })->throws(
                function(\Exception $e) {}
            );
            Assert::that(function() { throw new \RuntimeException('foo'); })->throws(
                function(\Exception $e) {},
                'foo'
            );

            $actualException = new \RuntimeException();

            Assert::that(function() use ($actualException) { throw $actualException; })->throws(
                function(\Exception $e) use ($actualException) {
                    $this->assertSame($e, $actualException);
                }
            );
        });

        $this
            ->assertFails('No exception thrown. Expected "RuntimeException".', function() {
                Assert::that(function() {})->throws(\RuntimeException::class);
            })
            ->assertFails('No exception thrown. Expected "RuntimeException".', function() {
                Assert::that(function() {})->throws(function(\RuntimeException $e) {});
            })
            ->assertFails('Expected "RuntimeException" to be thrown but got "Exception".', function() {
                Assert::that(function() { throw new \Exception(); })->throws(\RuntimeException::class);
            })
            ->assertFails('Expected "RuntimeException" to be thrown but got "Exception".', function() {
                Assert::that(function() { throw new \Exception(); })->throws(function(\RuntimeException $e) {});
            })
            ->assertFails('Expected "RuntimeException" message "bar" to contain "foo".', function() {
                Assert::that(function() { throw new \RuntimeException('bar'); })
                    ->throws(\RuntimeException::class, 'foo')
                ;
            })
        ;
    }

    /**
     * @test
     */
    public function can_chain_expectaions(): void
    {
        $this->assertSuccess(4, function() {
            Assert::that(['foo', 'bar'])
                ->hasCount(2)
                ->contains('foo')
                ->contains('bar')
                ->doesNotContain('baz')
            ;
        });
    }

    /**
     * @test
     */
    public function can_use_and(): void
    {
        $this->assertSuccess(3, function() {
            Assert::that(['foo', 'bar'])
                ->hasCount(2)
                ->contains('foo')
                ->and('foobar')
                ->contains('bar')
            ;
        });
    }

    private function assertSuccess(int $expectedCount, callable $what): self
    {
        $this->handler->reset();

        $what();

        $this->assertSame(
            $expectedCount,
            $this->handler->successCount(),
            \sprintf(
                'Expected %s successes but got %s (last failure: "%s").',
                $expectedCount,
                $this->handler->successCount(),
                $this->handler->failureCount() ? $this->handler->lastFailureMessage() : 'no message'
            )
        );

        return $this;
    }

    private function assertFails(string $expectedMessage, callable $what): self
    {
        $this->handler->reset();

        $what();

        $this->assertSame(1, $this->handler->failureCount(), 'Did not fail.');
        $this->assertSame($expectedMessage, $this->handler->lastFailureMessage());

        return $this;
    }
}
