<?php

namespace Zenstruck\Assert\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ComparisonAssertion extends EvaluableAssertion
{
    private const SAME = 'the same as';
    private const EQUAL = 'equal to';
    private const GREATER_THAN = 'greater than';
    private const GREATER_THAN_OR_EQUAL = 'greater than or equal to';
    private const LESS_THAN = 'less than';
    private const LESS_THAN_OR_EQUAL = 'less than or equal to';

    /** @var mixed */
    private $expected;

    /** @var mixed */
    private $actual;

    /** @var string */
    private $comparison;

    private function __construct($expected, $actual, string $comparison, ?string $message = null, array $context = [])
    {
        $this->expected = $expected;
        $this->actual = $actual;
        $this->comparison = $comparison;

        parent::__construct($message, $context);
    }

    /**
     * Asserts $expected and $actual are "the same" using "===".
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function same($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::SAME, $message, $context);
    }

    /**
     * Asserts $expected and $actual are "equal" using "==".
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function equal($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::EQUAL, $message, $context);
    }

    /**
     * Asserts $expected is "greater than" $actual.
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function greaterThan($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::GREATER_THAN, $message, $context);
    }

    /**
     * Asserts $expected is "greater than or equal to" $actual.
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function greaterThanOrEqual($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::GREATER_THAN_OR_EQUAL, $message, $context);
    }

    /**
     * Asserts $expected is "less than" $actual.
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function lessThan($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::LESS_THAN, $message, $context);
    }

    /**
     * Asserts $expected is "less than or equal to" $actual.
     *
     * @param mixed       $expected
     * @param mixed       $actual
     * @param string|null $message  Available context: {expected}, {actual}
     */
    public static function lessThanOrEqual($expected, $actual, ?string $message = null, array $context = []): self
    {
        return new self($expected, $actual, self::LESS_THAN_OR_EQUAL, $message, $context);
    }

    protected function evaluate(): bool
    {
        switch ($this->comparison) {
            case self::SAME:
                return $this->expected === $this->actual;
            case self::EQUAL:
                return $this->expected == $this->actual;
            case self::GREATER_THAN:
                return $this->expected > $this->actual;
            case self::GREATER_THAN_OR_EQUAL:
                return $this->expected >= $this->actual;
            case self::LESS_THAN:
                return $this->expected < $this->actual;
        }

        // less than or equal
        return $this->expected <= $this->actual;
    }

    protected function defaultFailureMessage(): string
    {
        if (self::SAME === $this->comparison && \is_scalar($this->actual) && \is_scalar($this->expected) && \gettype($this->actual) !== \gettype($this->expected)) {
            // show the type difference
            return \sprintf(
                'Expected "(%s) {expected}" to be %s "(%s) {actual}".',
                get_debug_type($this->expected),
                $this->comparison,
                get_debug_type($this->actual)
            );
        }

        return \sprintf('Expected "{expected}" to be %s "{actual}".', $this->comparison);
    }

    protected function defaultNotFailureMessage(): string
    {
        return \sprintf('Expected "{expected}" to not be %s "{actual}".', $this->comparison);
    }

    protected function defaultContext(): array
    {
        return ['expected' => $this->expected, 'actual' => $this->actual];
    }
}
