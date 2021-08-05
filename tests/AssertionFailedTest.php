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

        $exception = new AssertionFailed(
            'message %s %s %s %s %s %s %s %s',
            $expected = [
                1,
                'string',
                4.3,
                new \stdClass(),
                null,
                ['an', 'array'],
                'The quick brown fox jumps over the lazy dog',
                "string\nwith\n\rline\nbreak",
            ]
        );

        $this->assertSame('message 1 string 4.3 stdClass (null) (array) The quick brown fox jumps o...e lazy dog string with line break', $exception->getMessage());
        $this->assertSame($expected, $exception->context());
    }

    /**
     * @test
     */
    public function message_is_created_with_context_as_assoc_array(): void
    {
        $object = new \stdClass();
        $messageTemplate = 'message {int} {string1} {float} {object} {array} {string2} {string3}';
        $expectedMessage = 'message 1 value 4.3 stdClass (array) The quick brown fox jumps o...e lazy dog string with line break';
        $expectedContext = [
            'int' => 1,
            'string1' => 'value',
            'float' => 4.3,
            'object' => $object,
            'array' => ['foo'],
            'string2' => 'The quick brown fox jumps over the lazy dog',
            'string3' => "string\nwith\n\rline\nbreak",
        ];

        $exception = new AssertionFailed($messageTemplate, $expectedContext);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($expectedContext, $exception->context());

        $exception = new AssertionFailed($messageTemplate, [
            '{int}' => 1,
            '{string1}' => 'value',
            'float' => 4.3,
            '{object}' => $object,
            '{array}' => ['foo'],
            '{string2}' => 'The quick brown fox jumps over the lazy dog',
            '{string3}' => "string\nwith\n\rline\nbreak",
        ]);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($expectedContext, $exception->context());
    }
}
