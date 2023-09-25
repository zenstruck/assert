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
        $property->setValue(null, null);
    }
}
