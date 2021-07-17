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
        $this->context = self::denormalizeContext($context);

        parent::__construct(self::createMessage($message, $context), 0, $previous);
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

    private static function createMessage(string $template, array $context): string
    {
        // normalize context into scalar values
        $context = \array_map(
            static function($value) {
                if (\is_object($value)) {
                    return \get_class($value);
                }

                return \is_scalar($value) ? $value : \sprintf('(%s)', \gettype($value));
            },
            $context
        );

        if (!$context) {
            return $template;
        }

        if (array_is_list($context)) {
            return \sprintf($template, ...$context);
        }

        return \strtr($template, self::normalizeContext($context));
    }

    private static function normalizeContext(array $context): array
    {
        $newContext = [];

        foreach ($context as $key => $value) {
            if (!\preg_match('#^{.+}$#', $key)) {
                $key = "{{$key}}";
            }

            $newContext[$key] = $value;
        }

        return $newContext;
    }

    private static function denormalizeContext(array $context): array
    {
        $newContext = [];

        foreach ($context as $key => $value) {
            if (\preg_match('#^{(.+)}$#', $key, $matches)) {
                $key = $matches[1];
            }

            $newContext[$key] = $value;
        }

        return $newContext;
    }
}
