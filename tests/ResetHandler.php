<?php

namespace Zenstruck\Assert\Tests;

use Zenstruck\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
trait ResetHandler
{
    /**
     * @after
     */
    public static function resetHandler(): void
    {
        $property = (new \ReflectionClass(Assert::class))->getProperty('handler');
        $property->setAccessible(true);
        $property->setValue(null);
    }
}
