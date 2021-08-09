<?php

namespace Zenstruck\Assert\Tests\Fixture;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class IterableObject implements \IteratorAggregate
{
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
