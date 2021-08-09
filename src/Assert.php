<?php

namespace Zenstruck;

use Zenstruck\Assert\Assertion\ComparisonAssertion;
use Zenstruck\Assert\Assertion\ContainsAssertion;
use Zenstruck\Assert\Assertion\CountAssertion;
use Zenstruck\Assert\Assertion\EmptyAssertion;
use Zenstruck\Assert\Assertion\Negatable;
use Zenstruck\Assert\Assertion\ThrowsAssertion;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Handler;
use Zenstruck\Assert\Not;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Assert
{
    /** @var Handler|null */
    private static $handler;

    private function __construct()
    {
    }

    /**
     * @param callable():void $assertion Considered a "pass" if invoked successfully
     *                                   Considered a "fail" if {@see AssertionFailed} is thrown
     */
    public static function that(callable $assertion): void
    {
        try {
            $assertion();

            self::handler()->onSuccess();
        } catch (AssertionFailed $e) {
            self::handler()->onFailure($e);
        }
    }

    /**
     * @param Negatable $assertion Considered a "pass" if {@see AssertionFailed} is thrown when invoked
     *                             Considered a "fail" if {@see AssertionFailed} is NOT thrown when invoked
     */
    public static function not(Negatable $assertion): void
    {
        self::that(Not::wrap($assertion));
    }

    /**
     * @param bool $expression "pass" if true, "fail" if false
     */
    public static function true(bool $expression, string $message, array $context = []): void
    {
        self::that(static function() use ($expression, $message, $context) {
            if (!$expression) {
                AssertionFailed::throw($message, $context);
            }
        });
    }

    /**
     * @param bool $expression "pass" if false, "fail" if true
     */
    public static function false(bool $expression, string $message, array $context = []): void
    {
        self::true(!$expression, $message, $context);
    }

    /**
     * Trigger a generic assertion failure.
     */
    public static function fail(string $message, array $context = []): void
    {
        self::that(new AssertionFailed($message, $context));
    }

    /**
     * Trigger a generic assertion "pass".
     */
    public static function pass(): void
    {
        self::handler()->onSuccess();
    }

    /**
     * @see ThrowsAssertion::expect()
     */
    public static function throws($exception, callable $during): void
    {
        self::that(ThrowsAssertion::expect($exception, $during));
    }

    /**
     * @see EmptyAssertion::__construct()
     */
    public static function isEmpty($actual, ?string $message = null, array $context = []): void
    {
        self::that(new EmptyAssertion($actual, $message, $context));
    }

    /**
     * @see EmptyAssertion::__construct()
     */
    public static function isNotEmpty($actual, ?string $message = null, array $context = []): void
    {
        self::not(new EmptyAssertion($actual, $message, $context));
    }

    /**
     * @see CountAssertion::__construct()
     */
    public static function count(int $expected, $haystack, ?string $message = null, array $context = []): void
    {
        self::that(new CountAssertion($expected, $haystack, $message, $context));
    }

    /**
     * @see CountAssertion::__construct()
     */
    public static function notCount(int $expected, $haystack, ?string $message = null, array $context = []): void
    {
        self::not(new CountAssertion($expected, $haystack, $message, $context));
    }

    /**
     * @see ContainsAssertion::__construct()
     */
    public static function contains($needle, $haystack, ?string $message = null, array $context = []): void
    {
        self::that(new ContainsAssertion($needle, $haystack, $message, $context));
    }

    /**
     * @see ContainsAssertion::__construct()
     */
    public static function notContains($needle, $haystack, ?string $message = null, array $context = []): void
    {
        self::not(new ContainsAssertion($needle, $haystack, $message, $context));
    }

    /**
     * @see ComparisonAssertion::same()
     */
    public static function same($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::same($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::same()
     */
    public static function notSame($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::same($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::equal()
     */
    public static function equal($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::equal($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::equal()
     */
    public static function notEqual($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::equal($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::greaterThan()
     */
    public static function greaterThan($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::greaterThan($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::greaterThan()
     */
    public static function notGreaterThan($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::greaterThan($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::greaterThanOrEqual()
     */
    public static function greaterThanOrEqual($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::greaterThanOrEqual($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::greaterThanOrEqual()
     */
    public static function notGreaterThanOrEqual($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::greaterThanOrEqual($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::lessThan()
     */
    public static function lessThan($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::lessThan($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::lessThan()
     */
    public static function notLessThan($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::lessThan($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::lessThanOrEqual()
     */
    public static function lessThanOrEqual($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::that(ComparisonAssertion::lessThanOrEqual($expected, $actual, $message, $context));
    }

    /**
     * @see ComparisonAssertion::lessThanOrEqual()
     */
    public static function notLessThanOrEqual($expected, $actual, ?string $message = null, array $context = []): void
    {
        self::not(ComparisonAssertion::lessThanOrEqual($expected, $actual, $message, $context));
    }

    /**
     * Force a specific handler or use a custom one.
     */
    public static function useHandler(Handler $handler): void
    {
        self::$handler = $handler;
    }

    private static function handler(): Handler
    {
        if (self::$handler) {
            return self::$handler;
        }

        if (Handler\PHPUnitHandler::isSupported()) {
            return self::$handler = new Handler\PHPUnitHandler();
        }

        return self::$handler = new Handler\DefaultHandler();
    }
}
