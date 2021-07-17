<?php

namespace Zenstruck\Assert\Tests\Fixture;

use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TraceableHandler implements Handler
{
    /** @var int */
    private $successes = 0;

    /** @var AssertionFailed[] */
    private $failures = [];

    public function successCount(): int
    {
        return $this->successes;
    }

    /**
     * @return AssertionFailed[]
     */
    public function failures(): array
    {
        return $this->failures;
    }

    public function lastFailure(): AssertionFailed
    {
        if (false === $last = \end($this->failures)) {
            throw new \OutOfRangeException('No Failures.');
        }

        return $last;
    }

    public function onSuccess(): void
    {
        ++$this->successes;
    }

    public function onFailure(AssertionFailed $exception): void
    {
        $this->failures[] = $exception;
    }
}
