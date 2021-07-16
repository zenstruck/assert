<?php

namespace Zenstruck\Assert\Tests\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;
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
    public function php_unit_is_auto_detected_and_passing_assertions_add_to_assert_count(): void
    {
        $initialPHPUnitAssertionCount = PHPUnit::getCount();

        Assert::true(true, 'should not fail');
        Assert::false(false, 'should not fail');
        Assert::that(function() {});
        Assert::throws(\RuntimeException::class, function() { throw new \RuntimeException(); });
        Assert::throws(
            function(\RuntimeException $e) { $this->assertSame('some message', $e->getMessage()); },
            function() { throw new \RuntimeException('some message'); }
        );

        $this->assertSame(6, PHPUnit::getCount() - $initialPHPUnitAssertionCount);
    }

    /**
     * @test
     */
    public function php_unit_is_auto_detected_and_failing_assertion_triggers_php_unit_failure(): void
    {
        $initialPHPUnitAssertionCount = PHPUnit::getCount();

        try {
            Assert::true(false, 'this fails');
        } catch (AssertionFailedError $e) {
            $this->assertSame(1, PHPUnit::getCount() - $initialPHPUnitAssertionCount, 'The failure should trigger an assertion count');
            $this->assertSame('this fails', $e->getMessage());

            return;
        }

        PHPUnit::fail('No PHPUnit failure.');
    }
}
