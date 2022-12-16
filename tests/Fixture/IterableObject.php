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
 *
 * @implements \IteratorAggregate<int|string, mixed>
 */
final class IterableObject implements \IteratorAggregate
{
    /** @var array */
    private $value;

    public function __construct(array $value)
    {
        $this->value = $value;
    }

    public static function withCount(int $count): self
    {
        return new self(\array_fill(0, $count, null));
    }

    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->value);
    }
}
