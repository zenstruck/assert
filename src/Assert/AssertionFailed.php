<?php

namespace Zenstruck\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertionFailed extends \RuntimeException
{
    public function __construct(string $message, int $code = 0, ?\Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function __invoke(): void
    {
        throw $this;
    }

    /**
     * @psalm-return no-return
     */
    public static function throw(string $message, string ...$args): void
    {
        throw new self(\sprintf($message, ...$args));
    }

    /**
     * @psalm-return no-return
     */
    public static function throwWith(\Throwable $previous, string $message, string ...$args): void
    {
        throw new self(\sprintf($message, ...$args), 0, $previous);
    }
}
