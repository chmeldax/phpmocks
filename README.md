# PhpMocks

Making mocking a pleasure!

*Library to be used for testing classes*.

## Features

- Stubbing and mocking of all public class methods
- Supports abstract classes and interfaces
- Mocking of **static** methods
- Different behaviors of the mock **based on the method call parameters**
- Strict mocking by default (calls to non-mocked method are disabled by default)
- Fluent intuitive interface

## Installation

```
composer require chmeldax/phpmocks
```

## Usage


### Stubs

When you want to replace default behavior of the class, but you are not testing
the method calls themselves, then use `allowMethodCall()`.

```php
$doubleBuilder = new \Chmeldax\PhpMocks\Double\Builder($className);
$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, null) // Specify allowed parameters
    ->andReturn('return_value_1');
$double = $doubleBuilder->build(); // <- This is the stub
```

#### Specifying return values

```php
$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, null) // Specify allowed parameters
    ->andReturn('return_value_1', 'return_value_2'); // <- returns different values on #1 and #2 call
```


```php
$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, null) // Specify allowed parameters
    ->andInvoke(function ($a, $b, $c) { // specify your own callback
        return 'return_value'
      });
```


```php
$doubleBuilder = new \Chmeldax\PhpMocks\Double\Builder($instance);
$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, null) // Specify allowed parameters
    ->andCallOriginal(); // Calls the original method from $instance
```

#### Multiple with blocks

```php
$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, 'first')
    ->andReturn('return_value_1'); // <- returns different values on #1 and #2 call

$doubleBuilder
    ->allowMethodCall('methodToBeStubbed')
    ->with(new Constraints\Anything, new Constraints\Anything, 'second') // <- different parameter value
    ->andReturn('return_value_2');

$double = $doubleBuilder->build();
$double->methodToBeStubbed(1, 2, 'first');  // returns 'return_value_1'
$double->methodToBeStubbed(1, 2, 'second'); // returns 'return_value_2'
```

See `tests/DoubleTest.php` for more examples.

### Mocks

When you are testing the interaction with class, use `expectMethodCall`.
It will allow you to specify expected calls.

```php
$doubleBuilder
    ->expectMethodCall('methodToBeMocked')
    ->with(new Constraints\Anything, new Constraints\Anything, null)
    ->times(10) // Expectation
    ->andReturn('return_value_1');
```

```php
$doubleBuilder
    ->expectMethodCall('methodToBeMocked')
    ->with(new Constraints\Anything, new Constraints\Anything, 'first')
    ->atCalls(1, 3) // Expectation for 1st and 3rd call (counted globally for all "with" blocks)
    ->andReturn('return_value_1');

$doubleBuilder
    ->expectMethodCall('methodToBeMocked')
    ->with(new Constraints\Anything, new Constraints\Anything, 'second')
    ->atCall(2) // Expectation for 2nd call (counted globally for all "with" blocks)
    ->andReturn('return_value_2');
```

#### Checking expectations

```php
$doubleBuilder->checkExpectations() // Throws exception if any expectation is not met
```

It is advised to use `tearDown()` (or similar method) in your testing framework.

See `tests/ExpectedMethodTest.php` for more examples.

## Things to implement

- Support for PHP 7 (mainly return values, scalar types)
- Implement better Constraints
- Clean the code
- Introduce true unit tests
