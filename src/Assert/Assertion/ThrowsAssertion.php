<?php

namespace Zenstruck\Assert\Assertion;

use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ThrowsAssertion implements Negatable
{
    /** @var string */
    private $expected;

    /** @var callable */
    private $during;

    /** @var callable */
    private $onCatch;

    private function __construct(string $expected, callable $during, callable $onCatch)
    {
        $this->expected = $expected;
        $this->during = $during;
        $this->onCatch = $onCatch;
    }

    public function __invoke(): void
    {
        try {
            ($this->during)();
        } catch (\Throwable $actual) {
            if (!$actual instanceof $this->expected) {
                AssertionFailed::throw('Expected "{expected}" to be thrown but got "{actual}".', ['expected' => $this->expected, 'actual' => $actual]);
            }

            ($this->onCatch)($actual);

            return;
        }

        AssertionFailed::throw('No exception thrown. Expected "{expected}".', ['expected' => $this->expected]);
    }

    /**
     * @param string|callable(\Throwable):void $exception string: class name of the expected exception
     *                                                    callable: uses the first argument's type-hint
     *                                                    to determine the expected exception class. When
     *                                                    exception is caught, callable is invoked with
     *                                                    the caught exception
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

        if (!\is_string($exception)) {
            throw new \InvalidArgumentException(\sprintf('Expected exception must a string representation of a class or interface, "%s" given.', get_debug_type($exception)));
        }

        if (!\class_exists($exception) && !\interface_exists($exception)) {
            throw new \InvalidArgumentException(\sprintf('Expected exception must be an object or interface, "%s" given.', $exception));
        }

        return new self($exception, $during, $onCatch);
    }

    public function notFailure(): AssertionFailed
    {
        return new AssertionFailed('Expected "{expected}" to NOT be thrown but it was.', ['expected' => $this->expected]);
    }
}
