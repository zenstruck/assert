<?php

namespace Zenstruck\Assert\Tests;

use Zenstruck\Assert;
use Zenstruck\Assert\Tests\Fixture\TraceableHandler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait HasTraceableHandler
{
    use ResetHandler;

    /** @var TraceableHandler */
    private $handler;

    /**
     * @before
     */
    protected function configureHandler(): void
    {
        Assert::useHandler($this->handler = new TraceableHandler());
    }
}
