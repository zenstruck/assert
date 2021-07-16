<?php

namespace Zenstruck\Assert\Assertion;

use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThrowsAssertion
{
    /** @var string */
    private $expectedException;

    /** @var callable */
    private $what;

    /** @var callable[] */
    private $after;

    /** @var string */
    private $notThrownMessage = 'No exception thrown. Expected "%s".';

    /** @var string[] */
    private $notThrownArgs = [];

    /** @var string */
    private $mismatchMessage = 'No exception thrown. Expected "%s".';

    /** @var string[] */
    private $mismatchArgs = [];

    private function __construct(string $expectedException, callable $what, callable $after)
    {
        $this->expectedException = $expectedException;
        $this->what = $what;
        $this->after = [$after];
    }

    public function __invoke(): void
    {
        try {
            ($this->what)();
        } catch (\Throwable $exception) {
            if (!$exception instanceof $this->expectedException) {
                AssertionFailed::throw($this->mismatchMessage, ...($this->mismatchArgs ?: [$this->expectedException]));
            }

            foreach ($this->after as $after) {
                $after($exception);
            }

            return;
        }

        AssertionFailed::throw($this->notThrownMessage, ...($this->notThrownArgs ?: [$this->notThrownArgs]));
    }

    /**
     * @param string|callable(\Throwable):void $exception string: class name of the expected exception
     *                                                    callable: uses the first argument's type-hint
     *                                                    to determine the expected exception class. When
     *                                                    exception is caught, callable is invoked with
     *                                                    the caught exception (useful for making follow-
     *                                                    up assertions on the exception and side-effect
     *                                                    assertions)
     * @param callable                         $what      Considered a "fail" if when invoked,
     *                                                    $expectedException isn't thrown
     */
    public static function expect($exception, callable $what): self
    {
        $after = static function() {};

        if (\is_callable($exception)) {
            $parameterRef = (new \ReflectionFunction(\Closure::fromCallable($exception)))->getParameters()[0] ?? null;

            if (!$parameterRef || !($type = $parameterRef->getType()) instanceof \ReflectionNamedType) {
                throw new \InvalidArgumentException('When $exception is a callback, the first parameter must be type-hinted as the expected exception.');
            }

            $after = $exception;
            $exception = $type->getName();
        }

        return new self($exception, $what, $after);
    }

    public function ifNotThrown(string $message, string ...$args): self
    {
        $this->notThrownMessage = $message;
        $this->notThrownArgs = $args;

        return $this;
    }

    public function ifMismatch(string $message, string ...$args): self
    {
        $this->mismatchMessage = $message;
        $this->mismatchArgs = $args;

        return $this;
    }
}
