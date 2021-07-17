<?php

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertionFailedTest extends TestCase
{
    /**
     * @test
     */
    public function message_is_created_with_context(): void
    {
        $exception = new AssertionFailed('message %s');

        $this->assertSame('message %s', $exception->getMessage());

        $exception = new AssertionFailed('message %s', [1]);

        $this->assertSame('message 1', $exception->getMessage());
    }

    /**
     * @test
     */
    public function can_access_context(): void
    {
        $exception = new AssertionFailed('message %s');

        $this->assertSame([], $exception->getContext());

        $exception = new AssertionFailed('message %s', [1]);

        $this->assertSame([1], $exception->getContext());
    }
}
