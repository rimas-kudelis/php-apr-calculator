# Average percentage rate of charge calculator for PHP

![Build status](https://github.com/rimas-kudelis/php-apr-calculator/actions/workflows/build.yml/badge.svg)

This library is an average percentage rate of charge (APR or APRC) calculator compliant* with European Commission [Directive 2014/17/EU (‘Mortgage Credit Directive’, MCD)](https://eur-lex.europa.eu/eli/dir/2014/17/oj) and European Commission [Directive 2008/48/EC (‘Consumer Credit Directive’, CCD)](https://eur-lex.europa.eu/eli/dir/2008/48/oj).

The APR is essentially how much your borrowing will cost over the period of an average year, over the term of your debt. It takes into account interest charged as well as any additional fees (such as arrangement fees, or annual fees) you’ll have to pay. It also considers the frequency with which interest is charged on your borrowing, as this as an impact on how much you will pay as well.

The code and logic was initially ported from a [similar C# library](https://github.com/stephenhaunts/UK-APR-Calculator), which claims compliance with the British Financial Conduct Authority (FCA) regulations, particularly the FCA [MBOC 10.3 Formular for calculating APR](https://www.handbook.fca.org.uk/handbook/MCOB/10/3.html). I believe that compliance with this regulation, as well as with [MCOB 10A.2 Formular for calculating APRC](https://www.handbook.fca.org.uk/handbook/MCOB/10A/2.html), is also safe to assume.

More information about the original library, as well as some examples of its usage, are available in [its author's archived weblog entry](https://web.archive.org/web/20210623190649/https://stephenhaunts.com/2013/05/22/how-to-calculate-annual-percentage-rates-apr/).

## How to use
Install the library using composer:
```shell
$ composer require rq/apr-calculator
```
Naturally, you can download the archive of this repository if you don’t want to or can’t use Composer for some reason.

Then in your code, create an instance of the Calculator class by passing the amount of the first advance to its constructor:
```php
use RQ\APRCalculator\Calculator;

...

$calculator = new Calculator(200000);
```

Once that is done, you must add all instalments (payments and advances) that are to be included in the calculation. What exactly these instalments are will depend completely on your use-case (type of the loan, its duration and other terms).

A single instalment may be added by using one of the following methods:
```php
// $calculator->addAdvance(float $amount, float $daysAfterFirstAdvance): self
// $calculator->addPayment(float $amount, float $daysAfterFirstAdvance): self
// $calculator->addInstalment(float $amount, float $daysAfterFirstAdvance, int $type = Calculator::TYPE_PAYMENT): self
$calculator->addAdvance(4000, 30);
$calculator->addPayment(4000, 30);
$calculator->addInstalment(4000, 30);
```

A series of regular identical instalments may be added like this:
```php
// $calculator->addRegularAdvances(float $amount, float $numberOfAdvances, float $daysBetweenAdvances, float $daysAfterFirstAdvance = 0): self
// $calculator->addRegularPayments(float $amount, float $numberOfPayments, float $daysBetweenPayments, float $daysAfterFirstAdvance = 0): self
// $calculator->aaddRegularInstalments(float $amount, float $numberOfInstalments, float $daysBetweenInstalments, float $daysAfterFirstAdvance = 0, int $type = Calculator::TYPE_PAYMENT): self
$calculator->addRegularAdvances(600, 12, Calculator::FREQUENCY_MONTHLY);
$calculator->addRegularPayments(600, 12, Calculator::FREQUENCY_MONTHLY);
$calculator->addRegularInstalments(600, 12, Calculator::FREQUENCY_MONTHLY);
```

Once all instalments have been added, just call the `calculate()` method to get the APR back as a float:
```php
// $calculator->calculate(float $guess = 0, int $round = self::DEFAULT_PRECISION): float
$apr = $calculator->calculate()
```

For your convenience, there is also a static `create()` method available, and all of `add*()` methods chainable, thus if you prefer, you can also calculate the APR without even assigning the calculator to a variable, like this:
```php
$apr = Calculator::create(200000)
    ->addPayment(4000, 0)
    ->addRegularPayments(1432.86, 240, Calculator::FREQUENCY_MONTHLY)
    ->calculate();
```

If there is only one (initial) advance and one payment in your scenario, you can further save one call by calling the `calculateForSinglePayment()` method right after you instantiate the class, like this:
```php
// $calculator->calculateForSinglePayment(float $payment, int $daysAfterAdvance, int $round = self::DEFAULT_PRECISION): float
$apr = Calculator::create(100)
    ->calculateForSinglePayment(120, 14);
```

A document with APR calculation examples published by the EC in 2015 [is available in the `doc/` folder](doc/aprc-examples-calculation_en.pdf). All of its examples have been rewritten as PhpSpec tests under `spec/`. They not only help test the code, but may also serve as illustration of how to use the library. Additionally, a number of tests written for the original C# library have also been ported and are available there.

---
\* Note of caution: at least according to the EC directives, in some cases the calculated APR will depend on the actual date that it is claimed on (leap years are involved in some cases). This is currently not taken into account by the calculator, and a year of 365.25 days is assumed. This means that there is a narrow chance of the result of the calculation actually being incorrect.
