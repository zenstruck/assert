<?php

namespace Zenstruck;

use Zenstruck\Assert\Assertion\ThrowsAssertion;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class Assert
{
    /** @var Handler|null */
    private static $handler;

    /**
     * @param callable():void $assertion Considered a "pass" if invoked successfully
     *                                   Considered a "fail" if {@see \Zenstruck\Assert\AssertionFailed} is thrown
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
        self::that(new AssertionFailed(\sprintf($message, $context)));
    }

    /**
     * @see ThrowsAssertion::expect()
     */
    public static function throws($exception, callable $during): void
    {
        self::that(ThrowsAssertion::expect($exception, $during));
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
