<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Assert\Tests;

use PHPUnit\Framework\TestCase;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Not;
use Zenstruck\Assert\Tests\Fixture\NegatableAssertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class NotTest extends TestCase
{
    /**
     * @test
     */
    public function passing_not(): void
    {
        $assertion = new NegatableAssertion(true);

        try {
            $assertion();
        } catch (AssertionFailed $e) {
            $this->assertSame('assertion failed', $e->getMessage());

            (new Not($assertion))(); // should pass

            return;
        }

        $this->fail('Assertion did not fail.');
    }

    /**
     * @test
     */
    public function failing_not(): void
    {
        $assertion = new NegatableAssertion(false);

        $assertion(); // should pass

        try {
            (new Not($assertion))();
        } catch (AssertionFailed $e) {
            $this->assertSame('negation failed', $e->getMessage());

            return;
        }

        $this->fail('Assertion did not fail.');
    }
}
