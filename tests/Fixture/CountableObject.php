<?php

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
