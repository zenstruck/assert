<?php

namespace Zenstruck\Assert;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class AssertionFailed extends \RuntimeException
{
    /** @var array */
    private $context;

    public function __construct(string $message, array $context = [], ?\Throwable $previous = null)
    {
        $this->context = $context;

        parent::__construct(\sprintf($message, ...$context), 0, $previous);
    }

    public function __invoke(): void
    {
        throw $this;
    }

    /**
     * Create and throw.
     *
     * @psalm-return no-return
     */
    public static function throw(string $message, array $context = [], ?\Throwable $previous = null): void
    {
        throw new self($message, $context, $previous);
    }

    public function getContext(): array
    {
        return $this->context;
    }
}
