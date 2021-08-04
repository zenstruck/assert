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
    public function message_is_created_with_context_as_list_array(): void
    {
        $exception = new AssertionFailed('message %s');

        $this->assertSame('message %s', $exception->getMessage());
        $this->assertSame([], $exception->context());

        $exception = new AssertionFailed('message %s %s %s %s %s', [1, 'string', 4.3, $object = new \stdClass(), ['foo']]);

        $this->assertSame('message 1 string 4.3 stdClass (array)', $exception->getMessage());
        $this->assertSame(
            [
                1,
                'string',
                4.3,
                $object,
                ['foo'],
            ],
            $exception->context()
        );
    }

    /**
     * @test
     */
    public function message_is_created_with_context_as_assoc_array(): void
    {
        $object = new \stdClass();
        $messageTemplate = 'message {int} {string} {float} {object} {array}';
        $expectedMessage = 'message 1 value 4.3 stdClass (array)';
        $expectedContext = [
            'int' => 1,
            'string' => 'value',
            'float' => 4.3,
            'object' => $object,
            'array' => ['foo'],
        ];

        $exception = new AssertionFailed($messageTemplate, $expectedContext);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($expectedContext, $exception->context());

        $exception = new AssertionFailed($messageTemplate, [
            '{int}' => 1,
            '{string}' => 'value',
            'float' => 4.3,
            '{object}' => $object,
            '{array}' => ['foo'],
        ]);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($expectedContext, $exception->context());
    }
}
