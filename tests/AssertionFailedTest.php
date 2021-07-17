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

        $exception = new AssertionFailed('message %s %s %s %s %s', [1, 'string', 4.3, new \stdClass(), ['v']]);

        $this->assertSame('message 1 string 4.3 stdClass (array)', $exception->getMessage());
    }

    /**
     * @test
     */
    public function can_access_raw_context(): void
    {
        $exception = new AssertionFailed('message %s');

        $this->assertSame([], $exception->getContext());

        $exception = new AssertionFailed('message %s %s %s %s %s', [
            1, 'string', 4.3, $object = new \stdClass(), ['array'],
        ]);

        $this->assertSame(
            [
                1,
                'string',
                4.3,
                $object,
                ['array'],
            ],
            $exception->getContext()
        );
    }
}
