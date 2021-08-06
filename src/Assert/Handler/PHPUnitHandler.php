<?php

namespace Zenstruck\Assert\Handler;

use PHPUnit\Framework\Assert as PHPUnit;
use SebastianBergmann\Exporter\Exporter;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Handler;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 *
 * @internal
 */
final class PHPUnitHandler implements Handler
{
    public function onSuccess(): void
    {
        // trigger a successful PHPUnit assertion to avoid "risky" tests
        PHPUnit::assertTrue(true);
    }

    public function onFailure(AssertionFailed $exception): void
    {
        PHPUnit::fail(self::failureMessage($exception));
    }

    public static function isVerbose(): bool
    {
        return \in_array('--verbose', $_SERVER['argv'], true) || \in_array('-v', $_SERVER['argv'], true);
    }

    public static function isSupported(): bool
    {
        return \class_exists(PHPUnit::class);
    }

    private static function failureMessage(AssertionFailed $exception): string
    {
        $message = $exception->getMessage();

        if (!($context = $exception->context()) || !self::isVerbose()) {
            return $message;
        }

        $message .= "\n\nFailure Context:\n\n";
        $exporter = new Exporter();

        foreach ($context as $name => $value) {
            $exported = $exporter->export($value);

            if (\mb_strlen($exported) > 5000) {
                // prevent ridiculously long objects
                $exported = $exporter->shortenedExport($value);
            }

            $message .= \sprintf("[%s]\n%s\n\n", $name, $exported);
        }

        return $message;
    }
}
