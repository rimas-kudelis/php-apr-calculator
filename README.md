# Average percentage rate of charge calculator for PHP

![Build status](https://api.travis-ci.org/rimas-kudelis/php-apr-calculator.svg?branch=master)

This library is an average percentage rate of charge (APR or APRC) calculator compliant* with European Commission [Directive 2014/17/EU (‘Mortgage Credit Directive’, MCD)](https://eur-lex.europa.eu/eli/dir/2014/17/oj) and European Commission [Directive 2008/48/EC (‘Consumer Credit Directive’, CCD)](https://eur-lex.europa.eu/eli/dir/2008/48/oj).

The APR is essentially how much your borrowing will cost over the period of an average year, over the term of your debt. It takes into account interest charged as well as any additional fees (such as arrangement fees, or annual fees) you’ll have to pay. It also considers the frequency with which interest is charged on your borrowing, as this as an impact on how much you will pay as well.

The code and logic was initially ported from a [similar C# library](https://github.com/stephenhaunts/UK-APR-Calculator), which claims compliance with the British Financial Conduct Authority (FCA) regulations, particularly the FCA [MBOC 10.3 Formular for calculating APR](https://www.handbook.fca.org.uk/handbook/MCOB/10/3.html). I believe that compliance with this regulation, as well as with [MCOB 10A.2 Formular for calculating APRC](https://www.handbook.fca.org.uk/handbook/MCOB/10A/2.html), is also safe to assume.

More information about the original library, as well as some examples of its usage, are available at https://stephenhaunts.com/2013/05/22/how-to-calculate-annual-percentage-rates-apr/.

## How to use
A document with APR calculation examples published by the EC in 2015 [is available in the `doc/` folder](doc/aprc-examples-calculation_en.pdf). All of its examples have been rewritten as PhpSpec tests under `spec/`. They not only help test the code, but may also serve as illustration of how to use the library. Additionally, a number of tests written for the original C# library have also been ported and are available there.

---
\* Note of caution: at least according to the EC directives, in some cases the calculated APR will depend on the actual date that it is claimed on (leap years are involved in some cases). This is currently not taken into account by the calculator, and a year of 365.25 days is assumed. This means that there is a narrow chance of the result of the calculation actually being incorrect.