<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Assert\Tests\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\TestCase;
use Zenstruck\Assert;
use Zenstruck\Assert\Handler\PHPUnitHandler;
use Zenstruck\Assert\Tests\AssertionFailedTest;
use Zenstruck\Assert\Tests\Fixture\CountableObject;
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
                'string' => AssertionFailedTest::LONG,
                'array' => ['an' => 'array'],
                'object' => $this,
            ]);
        } catch (AssertionFailedError $e) {
            $this->assertSame(\sprintf('this fails %s (array:assoc) %s', AssertionFailedTest::SHORT, __CLASS__), $e->getMessage());

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
                'string' => AssertionFailedTest::LONG,
                'array' => ['an' => 'array'],
                'object1' => $this,
                'object2' => new NegatableAssertion(true),
            ]);
        } catch (AssertionFailedError $e) {
            $this->assertStringContainsString(
                \sprintf("this fails %s (array:assoc) %s %s\n\nFailure Context:", AssertionFailedTest::SHORT, __CLASS__, NegatableAssertion::class),
                $e->getMessage()
            );
            $this->assertStringContainsString(\sprintf("[string]\n'%s'", AssertionFailedTest::LONG), $e->getMessage());
            $this->assertStringContainsString("[array]\nArray &0 (\n    'an' => 'array'\n)", $e->getMessage());
            $this->assertStringContainsString(\sprintf("[object1]\n%s Object (...)", __CLASS__), $e->getMessage());
            $this->assertStringContainsString(\sprintf("object2]\n%s Object &", NegatableAssertion::class), $e->getMessage());
            $this->assertStringContainsString("    'fail' => true\n)", $e->getMessage());

            return;
        }

        PHPUnit::fail('No PHPUnit failure.');
    }

    /**
     * @test
     */
    public function php_unit_displays_comparisons_if_applicable(): void
    {
        $this->assertComparison(function() { Assert::that('foo')->is('bar'); }, ["-'bar'", "+'foo'"]);
        $this->assertComparison(function() { Assert::that('foo')->equals('bar'); });
        $this->assertComparison(function() { Assert::that('6')->is('bar'); });
        $this->assertComparison(function() { Assert::that('6')->equals('bar'); });
        $this->assertComparison(function() { Assert::that(['foo'])->is(['bar']); }, ["-    0 => 'bar'", "+    0 => 'foo'"]);
        $this->assertComparison(function() { Assert::that(['foo'])->equals(['bar']); });
        $this->assertComparison(function() { Assert::that(new \DateTime('yesterday'))->equals(new \DateTime()); });
        $this->assertComparison(function() { Assert::that(new \DateTime('yesterday'))->equals(new \DateTimeImmutable()); });
        $this->assertComparison(
            function() { Assert::that(new CountableObject(2))->equals(new CountableObject(3)); },
            [
                "-    'count' => 3",
                "+    'count' => 2",
                ' Zenstruck\\Assert\\Tests\\Fixture\\CountableObject Object (',
            ]
        );

        $this->assertNoComparison(function() { Assert::that(6)->is('bar'); });
        $this->assertNoComparison(function() { Assert::that(['foo', 'bar'])->isNotEqualTo(['foo', 'bar']); });
        $this->assertNoComparison(function() { Assert::that('6')->isNotEqualTo(6); });
        $this->assertNoComparison(function() { Assert::that('6')->isNot('6'); });
        $this->assertNoComparison(function() { Assert::that('6')->is(6); });
        $this->assertNoComparison(function() { Assert::that(6)->isGreaterThan(7); });
        $this->assertNoComparison(function() { Assert::that(new \DateTime('today'))->isGreaterThan(new \DateTime('tomorrow')); });
        $this->assertNoComparison(function() { Assert::that(new CountableObject(2))->isNotEqualTo(new CountableObject(2)); });
        $this->assertNoComparison(function() { Assert::that(new CountableObject(2))->is(new CountableObject(2)); });
        $this->assertNoComparison(function() { Assert::that(new CountableObject(2))->is(new CountableObject(3)); });
    }

    /**
     * @test
     */
    public function verbose_output_does_not_contain_comparison(): void
    {
        if (!PHPUnitHandler::isVerbose()) {
            $this->markTestSkipped('Skip if not verbose.');
        }

        try {
            Assert::that('foo')->equals('bar');
        } catch (AssertionFailedError $e) {
            $this->assertStringContainsString('[expected]', $e->getMessage());
            $this->assertStringContainsString('[actual]', $e->getMessage());
            $this->assertStringNotContainsString('[compare_expected]', $e->getMessage());
            $this->assertStringNotContainsString('[compare_actual]', $e->getMessage());

            return;
        }

        $this->fail('Did not fail.');
    }

    private function assertComparison(callable $callback, array $expectedStrings = []): void
    {
        try {
            $callback();
        } catch (AssertionFailedError $e) {
            $this->assertStringContainsString("--- Expected\n+++ Actual\n@@ @@", $e->getMessage());

            foreach ($expectedStrings as $string) {
                $this->assertStringContainsString($string, $e->getMessage());
            }

            return;
        }

        $this->fail('Did not fail.');
    }

    private function assertNoComparison(callable $callback): void
    {
        try {
            $callback();
        } catch (AssertionFailedError $e) {
            $this->assertStringNotContainsString('--- Expected', $e->getMessage());

            return;
        }

        $this->fail('Did not fail.');
    }
}
