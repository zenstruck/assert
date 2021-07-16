<?php

namespace Zenstruck;

use Zenstruck\Assert\Exception\AssertionFailed;
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
     * @param callable                         $what              Considered a "fail" if when invoked,
     *                                                            $expectedException isn't thrown
     * @param string|callable(\Throwable):void $expectedException string: class name of the expected exception
     *                                                            callable: uses the first argument's type-hint
     *                                                            to determine the expected exception class. When
     *                                                            exception is caught, callable is invoked with
     *                                                            the caught exception (useful for making follow-
     *                                                            up assertions on the exception and side-effect
     *                                                            assertions)
     */
    public static function throws(callable $what, $expectedException): void
    {
        $postCallback = static function() {};

        if (\is_callable($expectedException)) {
            $parameterRef = (new \ReflectionFunction(\Closure::fromCallable($expectedException)))->getParameters()[0] ?? null;

            if (!$parameterRef || !($type = $parameterRef->getType()) instanceof \ReflectionNamedType) {
                throw new \InvalidArgumentException('When $expectedException is a callback, the first parameter must be type-hinted as the expected exception.');
            }

            $expectedException = $type->getName();
        }

        self::that(function() use ($what, $expectedException, $postCallback) {
            try {
                $what();
            } catch (\Throwable $exception) {
                if ($exception instanceof $expectedException) {
                    $postCallback();

                    return;
                }

                AssertionFailed::throw('Exception "%s" thrown but expected "%s".', \get_class($exception), $expectedException);
            }

            AssertionFailed::throw('No exception thrown. Expected "%s".', $expectedException);
        });
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
