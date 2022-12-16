<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

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

    public function failureCount(): int
    {
        return \count($this->failures);
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

    public function lastFailureMessage(): string
    {
        return $this->lastFailure()->getMessage();
    }

    public function onSuccess(): void
    {
        ++$this->successes;
    }

    public function onFailure(AssertionFailed $exception): void
    {
        $this->failures[] = $exception;
    }

    public function reset(): void
    {
        $this->failures = [];
        $this->successes = 0;
    }
}
