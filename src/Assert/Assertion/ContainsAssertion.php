<?php

namespace Zenstruck\Assert\Assertion;

/**
 * @author Kevin Bond <kevinbond@gmail.com>
 */
final class ContainsAssertion extends EvaluableAssertion
{
    /** @var mixed */
    private $needle;

    /** @var iterable|scalar */
    private $haystack;

    /**
     * @param mixed           $needle   If $haystack is scalar, must also be scalar
     * @param iterable|scalar $haystack scalar: will assert contains needle
     *                                  iterable: will assert needle is one of the elements
     * @param string|null     $message  Available context: {needle}, {haystack}
     */
    public function __construct($needle, $haystack, ?string $message = null, array $context = [])
    {
        if (!\is_scalar($haystack) && !\is_iterable($haystack)) {
            throw new \InvalidArgumentException(\sprintf('$haystack must be iterable or scalar, "%s" given.', get_debug_type($haystack)));
        }

        if (\is_scalar($haystack) && !\is_scalar($needle)) {
            throw new \InvalidArgumentException(\sprintf('When $haystack is scalar, $needle must also be scalar, "%s" given.', get_debug_type($needle)));
        }

        $this->needle = $needle;
        $this->haystack = $haystack;

        parent::__construct($message, $context);
    }

    protected function evaluate(): bool
    {
        if (\is_scalar($this->haystack)) {
            return str_contains((string) $this->haystack, (string) $this->needle);
        }

        $array = $this->haystack instanceof \Traversable ? \iterator_to_array($this->haystack) : $this->haystack;

        return \in_array($this->needle, $array, true);
    }

    protected function defaultFailureMessage(): string
    {
        return 'Expected "{haystack}" to contain "{needle}".';
    }

    protected function defaultNotFailureMessage(): string
    {
        return 'Expected "{haystack}" to not contain "{needle}".';
    }

    protected function defaultContext(): array
    {
        return ['needle' => $this->needle, 'haystack' => $this->haystack];
    }
}
