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
    public static function true(bool $expression, string $message, string ...$args): void
    {
        self::that(static function() use ($expression, $message, $args) {
            if (!$expression) {
                AssertionFailed::throw($message, ...$args);
            }
        });
    }

    /**
     * @param bool $expression "pass" if false, "fail" if true
     */
    public static function false(bool $expression, string $message, string ...$args): void
    {
        self::true(!$expression, $message, ...$args);
    }

    /**
     * Trigger a generic assertion failure.
     */
    public static function fail(string $message, string ...$args): void
    {
        self::that(new AssertionFailed(\sprintf($message, ...$args)));
    }

    /**
     * @see ThrowsAssertion::expect()
     */
    public static function throws($exception, callable $what): void
    {
        self::that(ThrowsAssertion::expect($exception, $what));
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

        if (Handler\PHPUnitHandler::supported()) {
            return self::$handler = new Handler\PHPUnitHandler();
        }

        return self::$handler = new Handler\DefaultHandler();
    }
}
