<?php

namespace Zenstruck\Assert\Assertion;

/**
 * @author Nicolas PHILIPPE <nikophil@gmail.com>
 */
final class ArraySubsetAssertion extends EvaluableAssertion
{
    private const MODE_IS_SUBSET = 'IS_SUBSET';
    private const MODE_HAS_SUBSET = 'HAS_SUBSET';

    private const PARAM_TYPE_NEEDLE = 'needle';
    private const PARAM_TYPE_HAYSTACK = 'haystack';

    /** @var array */
    private $needle;

    /** @var array */
    private $haystack;

    /** @var string */
    private $mode;

    /**
     * @param string|iterable $needle
     * @param string|iterable $haystack
     * @param string|null     $message  Available context: {needle}, {haystack}
     */
    private function __construct($needle, $haystack, string $mode, ?string $message = null, array $context = [])
    {
        $this->needle = $this->toArray($needle, self::PARAM_TYPE_NEEDLE);
        $this->haystack = $this->toArray($haystack, self::PARAM_TYPE_HAYSTACK);
        $this->mode = $mode;

        parent::__construct($message, $context);
    }

    /**
     * @param string|iterable $needle
     * @param string|iterable $haystack
     */
    public static function isSubsetOf($needle, $haystack, ?string $message = null, array $context = []): self
    {
        return new self($needle, $haystack, self::MODE_IS_SUBSET, $message, $context);
    }

    /**
     * @param string|iterable $haystack
     * @param string|iterable $needle
     */
    public static function hasSubset($haystack, $needle, ?string $message = null, array $context = []): self
    {
        return new self($needle, $haystack, self::MODE_HAS_SUBSET, $message, $context);
    }

    protected function evaluate(): bool
    {
        return $this->haystack === \array_replace_recursive($this->haystack, $this->needle);
    }

    protected function defaultFailureMessage(): string
    {
        return self::MODE_IS_SUBSET === $this->mode
            ? 'Expected needle to be a subset of haystack.'
            : 'Expected haystack to have needle as subset.';
    }

    protected function defaultNotFailureMessage(): string
    {
        return self::MODE_IS_SUBSET === $this->mode
            ? 'Expected needle not to be a subset of haystack.'
            : 'Expected haystack not to have needle as subset.';
    }

    protected function defaultContext(): array
    {
        return [
            'needle' => $this->needle,
            'haystack' => $this->haystack,
        ];
    }

    /**
     * @param string|iterable $haystackOrNeedle
     */
    private function toArray($haystackOrNeedle, string $role): array
    {
        if (\is_string($haystackOrNeedle)) {
            $jsonAsArray = \json_decode($haystackOrNeedle, true);
            if (!\is_array($jsonAsArray)) {
                throw new \InvalidArgumentException("Given string as {$role} is not a valid json list/object.");
            }

            return $jsonAsArray;
        }

        if (\is_array($haystackOrNeedle)) {
            return $haystackOrNeedle;
        }

        if ($haystackOrNeedle instanceof \ArrayObject) {
            return $haystackOrNeedle->getArrayCopy();
        }

        return \iterator_to_array($haystackOrNeedle);
    }
}
