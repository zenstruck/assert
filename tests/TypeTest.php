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
use Zenstruck\Assert\Type;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class TypeTest extends TestCase
{
    /**
     * @test
     */
    public function named_constructors(): void
    {
        $this->assertSame('bool', (string) Type::bool());
        $this->assertSame('string', (string) Type::string());
        $this->assertSame('int', (string) Type::int());
        $this->assertSame('float', (string) Type::float());
        $this->assertSame('numeric', (string) Type::numeric());
        $this->assertSame('array', (string) Type::array());
        $this->assertSame('array:assoc', (string) Type::arrayAssoc());
        $this->assertSame('array:list', (string) Type::arrayList());
        $this->assertSame('array:empty', (string) Type::arrayEmpty());
        $this->assertSame('callable', (string) Type::callable());
        $this->assertSame('resource', (string) Type::resource());
        $this->assertSame('object', (string) Type::object());
        $this->assertSame('iterable', (string) Type::iterable());
        $this->assertSame('countable', (string) Type::countable());
        $this->assertSame('string:json', (string) Type::json());
    }
}
