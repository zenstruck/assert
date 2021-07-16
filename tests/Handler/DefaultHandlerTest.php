<?php

namespace Zenstruck\Assert\Tests\Handler;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Exception\AssertionFailed;
use Zenstruck\Assert\Handler\DefaultHandler;
use Zenstruck\Assert\Tests\ResetHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class DefaultHandlerTest extends TestCase
{
    use ResetHandler;

    protected function setUp(): void
    {
        Assert::useHandler(new DefaultHandler());
    }

    /**
     * @test
     */
    public function failed_assertion_throws_exception(): void
    {
        Assert::true(true, 'message1');

        $this->expectException(AssertionFailed::class);
        $this->expectExceptionMessage('message2');

        Assert::true(false, 'message2');
    }
}
