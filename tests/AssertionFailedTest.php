<?php

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\AssertionFailed;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertionFailedTest extends TestCase
{
    public const LONG = "If you're looking for random paragraphs, you've come to the right place. When a random word or a random sentence isn't quite enough, the next logical step is to find a random paragraph. We created the Random Paragraph Generator with you in mind. The process is quite simple. Choose the number of random paragraphs you'd like to see and click the button. Your chosen number of paragraphs will instantly appear.";
    public const SHORT = "If you're looking for random paragraphs, you've come to t...ber of paragraphs will instantly appear.";

    /**
     * @test
     */
    public function message_is_created_with_context_as_list_array(): void
    {
        $exception = new AssertionFailed('message %s');

        $this->assertSame('message %s', $exception->getMessage());
        $this->assertSame([], $exception->context());

        $exception = new AssertionFailed(
            'message %s %s %s %s %s %s %s %s %s %s',
            $expected = [
                1,
                'string',
                4.3,
                new \stdClass(),
                null,
                ['an', 'array'],
                self::LONG,
                "string\nwith\n\rline\nbreak",
                false,
                true,
            ]
        );

        $this->assertSame(\sprintf('message 1 string 4.3 stdClass (null) (array) %s string with line break (false) (true)', self::SHORT), $exception->getMessage());
        $this->assertSame($expected, $exception->context());
    }

    /**
     * @test
     */
    public function message_is_created_with_context_as_assoc_array(): void
    {
        $object = new \stdClass();
        $messageTemplate = 'message {int} {string1} {float} {object} {array} {string2} {string3} {false} {true}';
        $expectedMessage = \sprintf('message 1 value 4.3 stdClass (array) %s string with line break (false) (true)', self::SHORT);
        $expectedContext = [
            'int' => 1,
            'string1' => 'value',
            'float' => 4.3,
            'object' => $object,
            'array' => ['foo'],
            'string2' => self::LONG,
            'string3' => "string\nwith\n\rline\nbreak",
            'false' => false,
            'true' => true,
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
            '{string2}' => self::LONG,
            '{string3}' => "string\nwith\n\rline\nbreak",
            '{false}' => false,
            '{true}' => true,
        ]);

        $this->assertSame($expectedMessage, $exception->getMessage());
        $this->assertSame($expectedContext, $exception->context());
    }
}
