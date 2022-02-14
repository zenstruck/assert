<?php

namespace Zenstruck;

use Zenstruck\Assert\Assertion\Negatable;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Expectation;
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
     * Execute an assertion.
     *
     * @param callable():void $assertion Considered a "pass" if invoked successfully
     *                                   Considered a "fail" if {@see AssertionFailed} is thrown
     */
    public static function run(callable $assertion): void
    {
        try {
            $assertion();

            self::handler()->onSuccess();
        } catch (AssertionFailed $e) {
            self::handler()->onFailure($e);
        }
    }

    /**
     * Execute a "negatable" assertion.
     *
     * @param Negatable $assertion Considered a "pass" if {@see AssertionFailed} is thrown when invoked
     *                             Considered a "fail" if {@see AssertionFailed} is NOT thrown when invoked
     */
    public static function not(Negatable $assertion): void
    {
        self::run(Not::wrap($assertion));
    }

    /**
     * @param bool $expression "pass" if true, "fail" if false
     */
    public static function true(bool $expression, string $message, array $context = []): void
    {
        self::run(static function() use ($expression, $message, $context) {
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
     *
     * @return never
     *
     * @throws \Throwable
     */
    public static function fail(string $message, array $context = []): void
    {
        self::run($e = new AssertionFailed($message, $context));

        throw $e;
    }

    /**
     * Trigger a generic assertion "pass".
     */
    public static function pass(): void
    {
        self::handler()->onSuccess();
    }

    /**
     * Returns the value of $callback and if an exception is thrown, trigger a "fail".
     *
     * @param string|null $message If not passed, use thrown exception message.
     *                             Available context: {exception}, {message}
     *
     * @return mixed The return value of executing $callback
     */
    public static function try(callable $callback, ?string $message = null, array $context = [])
    {
        try {
            return $callback();
        } catch (\Throwable $e) {
            self::run(new AssertionFailed(
                $message ?? $e->getMessage(),
                \array_merge(['exception' => $e, 'message' => $e->getMessage()], $context),
                $e
            ));
        }

        return null;
    }

    /**
     * Use the expectation API.
     *
     * @param mixed $value
     */
    public static function that($value): Expectation
    {
        return new Expectation($value);
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
