<?php

namespace Zenstruck\Assert\Assertion;

use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThrowsAssertion
{
    /** @var string */
    private $expected;

    /** @var callable */
    private $during;

    /** @var callable[] */
    private $onCatch;

    /** @var string */
    private $notThrownMessage = 'No exception thrown. Expected "%s".';

    /** @var string[] */
    private $notThrownArgs = [];

    /** @var string */
    private $mismatchMessage = 'No exception thrown. Expected "%s".';

    /** @var string[] */
    private $mismatchArgs = [];

    private function __construct(string $expected, callable $during, callable $onCatch)
    {
        $this->expected = $expected;
        $this->during = $during;
        $this->onCatch = [$onCatch];
    }

    public function __invoke(): void
    {
        try {
            ($this->during)();
        } catch (\Throwable $exception) {
            if (!$exception instanceof $this->expected) {
                AssertionFailed::throw($this->mismatchMessage, ...($this->mismatchArgs ?: [$this->expected]));
            }

            foreach ($this->onCatch as $callback) {
                $callback($exception);
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
     *                                                    the caught exception {@see onCatch()}
     * @param callable                         $during    Considered a "fail" if when invoked,
     *                                                    $exception isn't thrown
     */
    public static function expect($exception, callable $during): self
    {
        $onCatch = static function() {};

        if (\is_callable($exception)) {
            $parameterRef = (new \ReflectionFunction(\Closure::fromCallable($exception)))->getParameters()[0] ?? null;

            if (!$parameterRef || !($type = $parameterRef->getType()) instanceof \ReflectionNamedType) {
                throw new \InvalidArgumentException('When $exception is a callback, the first parameter must be type-hinted as the expected exception.');
            }

            $onCatch = $exception;
            $exception = $type->getName();
        }

        return new self($exception, $during, $onCatch);
    }

    /**
     * Invoked after the expected exception was successfully caught
     * with the exception as an argument. Can be used to perform
     * additional assertions on the exception itself or side-effect
     * assertions.
     *
     * @param callable(\Throwable):void $callback
     */
    public function onCatch(callable $callback): self
    {
        $this->onCatch[] = $callback;

        return $this;
    }

    /**
     * Customize the failure message if no exception was thrown.
     */
    public function ifNotThrown(string $message, string ...$args): self
    {
        $this->notThrownMessage = $message;
        $this->notThrownArgs = $args;

        return $this;
    }

    /**
     * Customize the failure message the exception thrown does not match
     * (or is not an instance of) the expected exception class.
     */
    public function ifMismatch(string $message, string ...$args): self
    {
        $this->mismatchMessage = $message;
        $this->mismatchArgs = $args;

        return $this;
    }
}
