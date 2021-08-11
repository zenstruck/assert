<?php

namespace Zenstruck\Assert\Tests\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Handler\PHPUnitHandler;
use Zenstruck\Assert\Tests\Fixture\NegatableAssertion;
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
        Assert::run(function() {});

        $this->assertSame(3, PHPUnit::getCount() - $initialPHPUnitAssertionCount);
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

    /**
     * @test
     */
    public function non_verbose_run_does_not_export_context(): void
    {
        if (PHPUnitHandler::isVerbose()) {
            $this->markTestSkipped('Skip if verbose.');
        }

        try {
            Assert::true(false, 'this fails {string} {array} {object}', [
                'string' => 'The quick brown fox jumps over the lazy dog',
                'array' => ['an' => 'array'],
                'object' => $this,
            ]);
        } catch (AssertionFailedError $e) {
            $this->assertSame(\sprintf('this fails The quick brown fox jumps o...e lazy dog (array) %s', __CLASS__), $e->getMessage());

            return;
        }

        PHPUnit::fail('No PHPUnit failure.');
    }

    /**
     * @test
     */
    public function verbose_run_exports_context(): void
    {
        if (!PHPUnitHandler::isVerbose()) {
            $this->markTestSkipped('Skip if not verbose.');
        }

        try {
            Assert::true(false, 'this fails {string} {array} {object1} {object2}', [
                'string' => 'The quick brown fox jumps over the lazy dog',
                'array' => ['an' => 'array'],
                'object1' => $this,
                'object2' => new NegatableAssertion(true),
            ]);
        } catch (AssertionFailedError $e) {
            $this->assertStringContainsString(
                \sprintf("this fails The quick brown fox jumps o...e lazy dog (array) %s %s\n\nFailure Context:", __CLASS__, NegatableAssertion::class),
                $e->getMessage()
            );
            $this->assertStringContainsString("[string]\n'The quick brown fox jumps over the lazy dog'", $e->getMessage());
            $this->assertStringContainsString("[array]\nArray &0 (\n    'an' => 'array'\n)", $e->getMessage());
            $this->assertStringContainsString(\sprintf("[object1]\n%s Object (...)", __CLASS__), $e->getMessage());
            $this->assertStringContainsString(\sprintf("object2]\n%s Object &", NegatableAssertion::class), $e->getMessage());
            $this->assertStringContainsString("    'fail' => true\n)", $e->getMessage());

            return;
        }

        PHPUnit::fail('No PHPUnit failure.');
    }
}
