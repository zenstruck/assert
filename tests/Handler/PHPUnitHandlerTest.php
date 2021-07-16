<?php

namespace Zenstruck\Assert\Tests\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Tests\ResetHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class PHPUnitHandlerTest extends TestCase
{
    use ResetHandler;

    /**
     * @test
     */
    public function phpunit_is_auto_detected_and_passing_assertions_add_to_assert_count(): void
    {
        $initialPHPUnitAssertionCount = PHPUnit::getCount();

        Assert::true(true, 'should not fail');
        Assert::false(false, 'should not fail');
        Assert::that(function() {});
        Assert::throws(function() { throw new \RuntimeException(); }, \RuntimeException::class);
        Assert::throws(function() { throw new \RuntimeException(); }, function(\RuntimeException $e) {});

        $this->assertSame(5, PHPUnit::getCount() - $initialPHPUnitAssertionCount);
    }
}
