# zenstruck/assert

This library allows dependency-free test assertions. When using a PHPUnit-based test
library (PHPUnit itself, pest, Codeception), failed assertions are automatically converted
to PHPUnit failures and successful assertions are added to PHPUnit's successful assertion
count.

This library differs from other popular assertion libraries
([webmozart/assert](https://github.com/webmozarts/assert) &
[beberlei/assert](https://github.com/beberlei/assert)) in that it is purely for _test assertions_
opposed to what these libraries provide: _type safety assertions_.

With the exception of [Throws Assertion](#throws-assertion) (which provides a nice API
for making exception assertions), this library is really only useful for 3rd party libraries that
would like to provide test assertions but not rely on a specific test library.

## Installation

```bash
$ composer require zenstruck/assert
```

## `Zenstruck\Assert`

This is the main entry point for making assertions. When the methods on this class
are called, they throw a `Zenstruck\Assert\AssertionFailed` on failure. If the
do not throw this exception, they are considered successful.

When using a PHPUnit-based framework, failed assertions are auto-converted to PHPUnit
test failures and successful assertions are added to PHPUnit's successful assertion
count.

## True/False Assertions

```php
use Zenstruck\Assert;

// passes
Assert::true(true === true, 'The condition was not true.');

// fails
Assert::true(true === false, 'The condition was not true.');

// passes
Assert::false(true === false, 'The condition was not false.');

// fails
Assert::false(true === true, 'The condition was not false.');
```

## Generic Fail/Pass


```php
use Zenstruck\Assert;

// trigger a "fail"
Assert::fail('This is a failure.');

// trigger a "pass"
Assert::pass();
```

## _That_ Assertions

`Assert::that()` executes a `callable`. A successful execution is considered
a pass and if `Zenstruck\Assert\AssertionFailed` is thrown, it is a fail.

```php
use Zenstruck\Assert;
use Zenstruck\Assert\AssertionFailed;

// failure
Assert::that(function(): void {
    if (true) {
        AssertionFailed::throw('This failed.');
    }
});

// pass
Assert::that(function(): void {
    if (false) {
        AssertionFailed::throw('This failed.');
    }
});
```

## Throws Assertion

This assertion provides a nice API for exceptions. It is an alternative to PHPUnit's
`expectException()` which has the following limitations:

1. Can only assert 1 exception is thrown per test.
2. Cannot make assertions on the exception itself (other than the message).
3. Cannot make post-exception assertions (think side effects).

```php
use Zenstruck\Assert;

// the following can all be used within a single PHPUnit test

// fails if exception not thrown
// fails if exception is thrown but not instance of \RuntimeException
Assert::throws(\RuntimeException::class, fn() => $code->thatThrowsException());

// a callable can be used for the expected exception. The first parameter's type
// hint is used as the expected exception and the callable is executed with the
// caught exception
//
// fails if exception not thrown
// fails if exception is thrown but not instance of CustomException
Assert::throws(
    function(CustomException $e) use ($database) {
        // can use standard PHPUnit assertions here on the exception itself
        $this->assertStringContainsString('some message', $e->getMessage());
        $this->assertSame('value', $e->getSomeValue());

        // can use standard PHPUnit assertions to test side-effects
        $this->assertTrue($database->userTableEmpty());
    },
    fn() => $code->thatThrowsException()
);
```

## `AssertionFailed` Exception

When triggering a failed assertions, it is important to provide a useful failure
message to the user. The `AssertionFailed` exception has some features to help.

```php
use Zenstruck\Assert\AssertionFailed;

// The `throw()` named constructor creates the exception and immediately throws it.
AssertionFailed::throw('Some message');

// second parameter can be used as sprintf values for the message
// message = 'Expected "value 1" but got "value 2"'
AssertionFailed::throw('Expected "%s" but got "%s"', ['value 1', 'value 2']);

// when an associated array passed as the second parameter, the message is constructed
// with a simple template system
// message = 'Expected "value 1" but got "value 2"'
AssertionFailed::throw('Expected "{expected}" but got "{actual}"', [
    'expected' => 'value 1',
    'actual' => 'value 2',
]);
```

## Assertion Objects

Since `Zenstruck\Assert::that()` accepts any `callable` complex assertions can be wrapped
up into `invokable` objects:

```php
use Zenstruck\Assert;
use Zenstruck\Assert\AssertionFailed;

class StringContains
{
    public function __construct(private string $haystack, private string $needle) {}
    
    public function __invoke(): void
    {
        if (!str_contains($this->haystack, $this->needle)) {
            AssertionFailed::throw(
                'Expected string "{haystack}" to contain "{needle}" but it did not.',
                get_object_vars($this)
            ]);
        }
    }
}

// use the above assertion:

// passes
Assert::that(new StringContains('quick brown fox', 'fox'));

// fails
Assert::that(new StringContains('quick brown fox', 'dog'));
```

## Negatable Assertion Objects

`Zenstruck\Assert` has a `not()` method that can be used with _Negatable_
[Assertion Objects](#assertion-objects). This can be helpful to create
custom assertions that can be easily negated. Let's convert the example above
into a _Negatable Assertion Object_:

```php
use Zenstruck\Assert;
use Zenstruck\Assert\AssertionFailed;
use Zenstruck\Assert\Assertion\Negatable;

class StringContains implements Negatable
{
    public function __construct(private string $haystack, private string $needle) {}
    
    public function __invoke(): void
    {
        if (!str_contains($this->haystack, $this->needle)) {
            AssertionFailed::throw(
                'Expected string "{haystack}" to contain "{needle}" but it did not.',
                get_object_vars($this)
            ]);
        }
    }
    
    public function negatableFailure(): AssertionFailed
    {
        return new AssertionFailed(
            'Expected string "{haystack}" to not contain "{needle}" but it did.',
            get_object_vars($this)
        );
    }
}

// use the above assertion:

// fails
Assert::not(new StringContains('quick brown fox', 'fox'));

// passes
Assert::not(new StringContains('quick brown fox', 'dog'));
```
