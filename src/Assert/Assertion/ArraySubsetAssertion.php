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
        return $this->doEvaluate($this->needle, $this->haystack);
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
            'mode' => $this->mode,
        ];
    }

    private function doEvaluate(array $needle, array $haystack): bool
    {
        // empty needle is always a subset of any haystack
        if ([] === $needle) {
            return true;
        }

        // empty haystack cannot have any subset except empty array
        if ([] === $haystack) {
            return false;
        }

        return array_is_list($needle)
            ? $this->doEvaluateArrayList($needle, $haystack)
            : $this->doEvaluateArrayAssoc($needle, $haystack);
    }

    private function doEvaluateArrayList(array $needle, array $haystack): bool
    {
        foreach ($needle as $needleValue) {
            if (\is_array($needleValue)) {
                $isValueInHaystack = false;
                foreach ($haystack as $haystackValue) {
                    if (!\is_array($haystackValue)) {
                        continue;
                    }

                    if ($this->doEvaluate($needleValue, $haystackValue)) {
                        $isValueInHaystack = true;
                        break;
                    }
                }

                if (false === $isValueInHaystack) {
                    return false;
                }
            } elseif (!\in_array($needleValue, $haystack, true)) {
                return false;
            }
        }

        return true;
    }

    private function doEvaluateArrayAssoc(array $needle, array $haystack): bool
    {
        foreach ($needle as $key => $value) {
            if (!\array_key_exists($key, $haystack)) {
                return false;
            }

            if (\is_array($value) && \is_array($haystack[$key])) {
                if (!$this->doEvaluate($value, $haystack[$key])) {
                    return false;
                }
            } elseif ($value !== $haystack[$key]) {
                return false;
            }
        }

        return true;
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
