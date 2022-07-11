<?php

namespace Zenstruck\Assert;

use Zenstruck\Assert;
use Zenstruck\Assert\Assertion\ComparisonAssertion;
use Zenstruck\Assert\Assertion\ContainsAssertion;
use Zenstruck\Assert\Assertion\CountAssertion;
use Zenstruck\Assert\Assertion\EmptyAssertion;
use Zenstruck\Assert\Assertion\ThrowsAssertion;
use Zenstruck\Assert\Assertion\TypeAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Expectation
{
    /** @var mixed */
    private $value;

    /**
     * @param mixed $value
     */
    public function __construct($value)
    {
        $this->value = $value;
    }

    /**
     * Start another expectation with a new value.
     *
     * @param mixed $value
     */
    public function and($value): self
    {
        return new self($value);
    }

    /**
     * Assert the expectation value is empty (if countable, has a count of 0).
     *
     * @param string|null $message Available context: {actual}, {count} (if countable)
     */
    public function isEmpty(?string $message = null, array $context = []): self
    {
        Assert::run(new EmptyAssertion($this->value, $message, $context));

        return $this;
    }

    /**
     * Assert the expectation value is NOT empty (if countable, has a count > 0).
     *
     * @param string|null $message Available context: {actual}
     */
    public function isNotEmpty(?string $message = null, array $context = []): self
    {
        Assert::not(new EmptyAssertion($this->value, $message, $context));

        return $this;
    }

    /**
     * Assert the expectation value is null.
     *
     * @param string|null $message Available context: {actual}
     */
    public function isNull(?string $message = null, array $context = []): self
    {
        Assert::true(
            null === $this->value,
            $message ?? 'Expected "{actual}" to be null.',
            \array_merge(['actual' => $this->value], $context)
        );

        return $this;
    }

    /**
     * Assert the expectation value is NOT null.
     */
    public function isNotNull(?string $message = null, array $context = []): self
    {
        Assert::false(null === $this->value, $message ?? 'Expected the value to not be null.', $context);

        return $this;
    }

    /**
     * Assert the expectation is an instance of $expected.
     *
     * @param string|null $message Available context: {actual}, {expected}
     */
    public function isInstanceOf(string $expected, ?string $message = null, array $context = []): self
    {
        Assert::true(
            $this->value instanceof $expected,
            $message ?? 'Expected "{actual}" to be an instance of "{expected}".',
            \array_merge(['expected' => $expected, 'actual' => $this->value], $context)
        );

        return $this;
    }

    /**
     * Assert the expectation is NOT an instance of $expected.
     *
     * @param string|null $message Available context: {actual}, {expected}
     */
    public function isNotInstanceOf(string $expected, ?string $message = null, array $context = []): self
    {
        Assert::false(
            $this->value instanceof $expected,
            $message ?? 'Expected "{actual}" to not be an instance of "{expected}".',
            \array_merge(['expected' => $expected, 'actual' => $this->value], $context)
        );

        return $this;
    }

    /**
     * Assert the expectation value has the expected $count.
     *
     * @param string|null $message Available context: {expected}, {actual}, {haystack}
     */
    public function hasCount(int $count, ?string $message = null, array $context = []): self
    {
        Assert::run(new CountAssertion($count, $this->value, $message, $context));

        return $this;
    }

    /**
     * Assert the expectation value does NOT have the expected $count.
     *
     * @param string|null $message Available context: {expected}, {haystack}
     */
    public function doesNotHaveCount(int $count, ?string $message = null, array $context = []): self
    {
        Assert::not(new CountAssertion($count, $this->value, $message, $context));

        return $this;
    }

    /**
     * Assert the expectation value contains the expected $needle. If the expectation
     * value is a string, str_contains() is used, if it's iterable, in_array() (strict)
     * is used.
     *
     * @param mixed       $needle
     * @param string|null $message Available context: {needle}, {haystack}
     */
    public function contains($needle, ?string $message = null, array $context = []): self
    {
        Assert::run(new ContainsAssertion($needle, $this->value, $message, $context));

        return $this;
    }

    /**
     * Assert the expectation value does NOT contain the expected $needle. If the expectation
     * value is a string, str_contains() is used, if it's iterable, in_array() (strict)
     * is used.
     *
     * @param mixed       $needle
     * @param string|null $message Available context: {needle}, {haystack}
     */
    public function doesNotContain($needle, ?string $message = null, array $context = []): self
    {
        Assert::not(new ContainsAssertion($needle, $this->value, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value and $expected are "equal" using "==".
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function equals($expected, ?string $message = null, array $context = []): self
    {
        Assert::run(ComparisonAssertion::equal($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value and $expected are NOT "equal" using "!=".
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isNotEqualTo($expected, ?string $message = null, array $context = []): self
    {
        Assert::not(ComparisonAssertion::equal($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value and $expected are "the same" using "===".
     *
     * If a {@see Type} object is passed as expected, asserts the type matches.
     *
     * @param mixed|Type  $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function is($expected, ?string $message = null, array $context = []): self
    {
        if ($expected instanceof Type) {
            Assert::run(new TypeAssertion($this->value, $expected, $message, $context));

            return $this;
        }

        Assert::run(ComparisonAssertion::same($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value and $expected are NOT "the same" using "!==".
     *
     * If a {@see Type} object is passed as expected, asserts the type DOES NOT matche.
     *
     * @param mixed|Type  $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isNot($expected, ?string $message = null, array $context = []): self
    {
        if ($expected instanceof Type) {
            Assert::not(new TypeAssertion($this->value, $expected, $message, $context));

            return $this;
        }

        Assert::not(ComparisonAssertion::same($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value is "greater than" $expected.
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isGreaterThan($expected, ?string $message = null, array $context = []): self
    {
        Assert::run(ComparisonAssertion::greaterThan($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value is "greater than or equal to" $expected.
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isGreaterThanOrEqualTo($expected, ?string $message = null, array $context = []): self
    {
        Assert::run(ComparisonAssertion::greaterThanOrEqual($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value is "less than" $expected.
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isLessThan($expected, ?string $message = null, array $context = []): self
    {
        Assert::run(ComparisonAssertion::lessThan($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Asserts the expectation value is "less than or equal to" $expected.
     *
     * @param mixed       $expected
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public function isLessThanOrEqualTo($expected, ?string $message = null, array $context = []): self
    {
        Assert::run(ComparisonAssertion::lessThanOrEqual($this->value, $expected, $message, $context));

        return $this;
    }

    /**
     * Executes the expectation value as a callable and asserts the $expectedException is thrown. When
     * $expectedException is a callable, it is executed with the caught exception enabling additional
     * assertions within. Optionally pass $expectedMessage to assert the caught exception contains
     * this value.
     *
     * @param string|callable $expectedException string: class name of the expected exception
     *                                           callable: uses the first argument's type-hint
     *                                           to determine the expected exception class. When
     *                                           exception is caught, callable is invoked with
     *                                           the caught exception
     * @param string|null     $expectedMessage   Assert the caught exception message "contains"
     *                                           this string
     */
    public function throws($expectedException, ?string $expectedMessage = null): self
    {
        Assert::run(new ThrowsAssertion($this->value, $expectedException, $expectedMessage));

        return $this;
    }
}
