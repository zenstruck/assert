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

    public function context(): array
    {
        return $this->context;
    }

    private static function createMessage(string $template, array $context): string
    {
        // normalize context into scalar values
        $context = \array_map([self::class, 'normalizeContextValue'], $context);

        if (!$context) {
            return $template;
        }

        if (array_is_list($context)) {
            return \sprintf($template, ...$context);
        }

        return \strtr($template, self::normalizeContext($context));
    }

    private static function normalizeContextValue($value): string
    {
        if (\is_object($value)) {
            return \get_class($value);
        }

        if (!\is_scalar($value)) {
            return \sprintf('(%s)', get_debug_type($value));
        }

        $value = \preg_replace('/\s+/', ' ', $value);

        if (\mb_strlen($value) <= 40) {
            return $value;
        }

        return \sprintf('%s...%s', \mb_substr($value, 0, 27), \mb_substr($value, -10));
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
