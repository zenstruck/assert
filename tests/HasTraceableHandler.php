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
