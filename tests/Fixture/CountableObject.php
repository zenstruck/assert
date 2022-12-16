<?php

/*
 * This file is part of the zenstruck/assert package.
 *
 * (c) Kevin Bond <kevinbond@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Zenstruck\Assert\Tests\Fixture;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class CountableObject implements \Countable
{
    /** @var int */
    private $count;

    public function __construct(int $count)
    {
        $this->count = $count;
    }

    public function count(): int
    {
        return $this->count;
    }
}
