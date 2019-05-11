<?php

namespace spec\RQ\APRCalculator;

use RQ\APRCalculator\Calculator;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalculatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(100); // Amount of first advance
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(Calculator::class);
    }

    function it_is_initializable_via_static_class_method()
    {
        self::create(100)->shouldHaveType(Calculator::class);
    }

    /**
     * The examples below are ported directly from the C# library tests.
     *
     * @return void
     */
    function it_returns_one_percent_for_one_payment_one_year_after_the_advance()
    {
        $this->addInstalment(101, 365);

        $this->calculate()->shouldReturn(1.0);
    }

    function it_returns_zero_for_one_payment_of_same_size_as_advance()
    {
        $this->addInstalment(100, 1);

        $this->calculate()->shouldReturn(0.0);
    }

    function it_calculates_one_125_pound_payment_after_31_days()
    {
        $this->addInstalment(125, 31);

        $this->calculate()->shouldReturn(1286.2);
    }

    function it_calculates_one_125_pound_payment_after_31_days_when_using_calculateForSinglePayment()
    {
        $this->calculateForSinglePayment(125, 31)->shouldReturn(1286.2);
    }

    function it_calculates_cfa_bank_overdraft()
    {
        $this->beConstructedWith(200);
        $this->addInstalment(350, 365.25 / 12);

        $this->calculate()->shouldReturn(82400.5);
    }

    function it_calculates_cfa_personal_loan()
    {
        $this->beConstructedWith(10000);
        $this->addRegularInstalments(222.44, 60, Calculator::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(12.7);
    }

    function it_calculates_cfa_short_term_loan()
    {
        $this->beConstructedWith(200);
        $this->addInstalment(250, 365.25 / 12);

        $this->calculate()->shouldReturn(1355.2);
    }

    function it_calculates_single_instalment_example_1()
    {
        $this->beConstructedWith(250);
        $this->addInstalment(319.97, 28);

        $this->calculate()->shouldReturn(2400.3);
    }

    function it_calculates_single_instalment_example_2()
    {
        $this->beConstructedWith(350);
        $this->addInstalment(447.97, 23);

        $this->calculate()->shouldReturn(4935.9);
    }

    function it_calculates_single_instalment_example_3()
    {
        $this->beConstructedWith(150);
        $this->addInstalment(191.99, 36);

        $this->calculate()->shouldReturn(1123.2);
    }

    function it_calculates_single_instalment_example_4()
    {
        $this->addInstalment(127.99, 26);

        $this->calculate()->shouldReturn(3103.4);
    }

    function it_calculates_single_instalment_example_5()
    {
        $this->beConstructedWith(280);
        $this->addInstalment(358.40, 25);

        $this->calculate()->shouldReturn(3584.2);
    }

    function it_calculates_single_instalment_example_6()
    {
        $this->beConstructedWith(400);
        $this->addInstalment(511.96, 36);

        $this->calculate()->shouldReturn(1122.9);
    }

    function it_calculates_single_instalment_example_7()
    {
        $this->beConstructedWith(250);
        $this->addInstalment(319.97, 27);

        $this->calculate()->shouldReturn(2716.8);
    }

    function it_calculates_single_instalment_example_8()
    {
        $this->beConstructedWith(150);
        $this->addInstalment(191.99, 9);

        $this->calculate()->shouldReturn(2238723.9);
    }

    function it_calculates_single_instalment_example_9()
    {
        $this->beConstructedWith(200);
        $this->addInstalment(255.98, 27);

        $this->calculate()->shouldReturn(2717.4);
    }

    function it_calculates_single_instalment_example_10()
    {
        $this->beConstructedWith(300);
        $this->addInstalment(383.97, 16);

        $this->calculate()->shouldReturn(27865.8);
    }

    function it_calculates_single_instalment_example_11()
    {
        $this->beConstructedWith(364.54);
        $this->addInstalment(450, 30);

        $this->calculate()->shouldReturn(1199.0);
    }

    function it_calculates_single_instalment_example_12()
    {
        $this->beConstructedWith(250);
        $this->addInstalment(319.98, 16);

        $this->calculate()->shouldReturn(27875.8);
    }

    /**
     * The ec_calculator_example_* tests are examples described in
     * https://ec.europa.eu/info/system/files/aprc-examples-calculation_en.pdf
     *
     * @return void
     */
    function it_calculates_ec_calculator_example_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86, 240, Calculator::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_1_with_arbitrary_precision()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86, 240, Calculator::FREQUENCY_MONTHLY);

        $this->calculate(0, 6)->shouldReturn(6.434412);
    }

    function it_calculates_ec_calculator_example_1_with_chained_calls()
    {
        self::create(200000)
            ->addInstalment(4000, 0)
            ->addRegularInstalments(1432.86, 240, Calculator::FREQUENCY_MONTHLY)

            ->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_2_case_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1433.57, 240, Calculator::FREQUENCY_MONTHLY, 3 + 31);

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_2_case_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1433.56, 240, Calculator::FREQUENCY_MONTHLY, 3 + 31);

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_2_case_3()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(16541.86, 20, Calculator::FREQUENCY_YEARLY, 3 + 31);

        $this->calculate()->shouldReturn(6.3);
    }

    function it_calculates_ec_calculator_example_3()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86 + round(200 / 12, 2), 240, Calculator::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(6.6);
    }

    function it_calculates_ec_calculator_example_4()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86 + round(200000 / 100 / 12, 2), 240, Calculator::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(7.9);
    }

    function it_calculates_ec_calculator_example_5()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1490.18, 240, Calculator::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(7.0);
    }

    function it_calculates_ec_calculator_example_6()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86, 240, Calculator::FREQUENCY_MONTHLY);
        $this->addInstalment(100, Calculator::DAYS_IN_YEAR * 20);

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_7()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1199.10, 180, Calculator::FREQUENCY_MONTHLY);
        $this->addInstalment(142097.69, 15 * Calculator::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_8_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1166.67, 240, Calculator::FREQUENCY_MONTHLY);
        $this->addInstalment(200000, 20 * Calculator::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(7.4);
    }

    function it_calculates_ec_calculator_example_8_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1166.67, 6, Calculator::FREQUENCY_MONTHLY);
        $this->addRegularInstalments(1398.33, 234, Calculator::FREQUENCY_MONTHLY, 7 / 12 * Calculator::DAYS_IN_YEAR);
        $this->addInstalment(200000, 20 * Calculator::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(8.9);
    }

    function it_calculates_ec_calculator_example_9()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $instalment = 1130.33;
        for ($year = 0; $year < 20; $year++) {
            $this->addRegularInstalments(
                round($instalment, 2),
                12,
                Calculator::FREQUENCY_MONTHLY,
                Calculator::DAYS_IN_YEAR / 12 + Calculator::DAYS_IN_YEAR * $year
            );

            $instalment = $instalment * 1.03;
        }

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_10()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $instalment = 1778.58;
        for ($year = 0; $year < 20; $year++) {
            $this->addRegularInstalments(
                // It seems the EC-supplied calculation works by rounding the instalment amount
                // at pay time, not at calculation time.
                round($instalment, 2),
                12,
                Calculator::FREQUENCY_MONTHLY,
                Calculator::DAYS_IN_YEAR / 12 + Calculator::DAYS_IN_YEAR * $year
            );
            $instalment = $instalment * 0.97;
        }

        $this->calculate()->shouldReturn(6.5);
    }

    function it_calculates_ec_calculator_example_11()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1500, 220, Calculator::FREQUENCY_MONTHLY);
        $this->addInstalment(407.70, Calculator::DAYS_IN_YEAR / 12 * 221);

        $this->calculate()->shouldReturn(6.5);
    }

    function it_calculates_ec_calculator_example_12()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $month = 1;
        while ($owed > 0) {
            $repayment = min(900, $owed);
            $interest = round($owed * 0.06 / 12, 2);
            $this->addInstalment(
                $repayment + $interest,
                Calculator::DAYS_IN_YEAR / 12 * $month++
            );

            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.5);
    }

    function it_calculates_ec_calculator_example_13()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $month = 0;
        $repayment = 200000 / 240;

        while ($owed > 0) {
            $interest = round($owed * 0.06 / 12, 2);
            $this->addInstalment(
                $repayment + $interest,
                Calculator::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.5);
    }

    function it_calculates_ec_calculator_example_14()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $month = 0;

        while ($owed > 0) {
            $repayment = min(max($owed * 0.02, 100), $owed);
            $interest = $owed * 0.06 / 12;
            $this->addInstalment(
                round($repayment + $interest, 2),
                Calculator::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.8);
    }

    function it_calculates_ec_calculator_example_15()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $month = 0;

        while ($owed > 0) {
            // The  credit  agreement provides  for  a  monthly  payment  of  2%  of  the
            // outstanding balance of capital and interest with a minimum of €300
            $owed += $owed * 0.06 / 12;
            $repayment = min(max($owed * 0.02, 300), $owed);
            $this->addInstalment(
                round($repayment, 2),
                Calculator::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.7);
    }

    // The calculation for Example 16 is explained to be same as for Example 13.

    function it_calculates_ec_calculator_example_17()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;

        for ($month = 1; $month < 12 * 15; $month++) {
            $repayment = max($owed * 0.02, 100);
            $interest = $owed * 0.06 / 12;
            $this->addInstalment(
                round($repayment + $interest, 2),
                Calculator::DAYS_IN_YEAR / 12 * $month
            );
            $owed -= $repayment;
        }

        $interest = $owed * 0.06 / 12;
        $this->addInstalment(
            round($owed + $interest, 2),
            Calculator::DAYS_IN_YEAR / 12 * $month
        );

        $this->calculate()->shouldReturn(6.8);
    }

    function it_calculates_ec_calculator_example_18_case_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;

        $this->addRegularInstalments(
            1429.01,
            240,
            Calculator::FREQUENCY_MONTHLY,
            14
        );

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_18_case_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;

        $this->addRegularInstalments(
            1437.54,
            240,
            Calculator::FREQUENCY_MONTHLY,
            20 + Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_19()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $principal = $owed / 240;

        for ($month = 1; $month <= 240; $month++) {
            $interest = $owed * 0.06 / 12;
            $instalment = round($principal + $interest, 2);
            if (($month - 1) % 24 === 0) {
                $instalment += 100;
            }
            $this->addInstalment(
                $instalment,
                Calculator::DAYS_IN_YEAR / 12 * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(6.5);
    }

    function it_calculates_ec_calculator_example_20()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            24,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1423.41,
            216,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 25
        );

        $this->calculate()->shouldReturn(6.2);
    }

    function it_calculates_ec_calculator_example_21_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1374.06,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(5.9);
    }

    function it_calculates_ec_calculator_example_21_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1530.61,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.2);
    }

    function it_calculates_ec_calculator_example_22_case_1_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1349.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1404.06,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(6.1);
    }

    function it_calculates_ec_calculator_example_22_case_1_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1349.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1515.81,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.1);
    }

    function it_calculates_ec_calculator_example_22_case_2_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1339.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1394.06,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        // NOTE: there seems to be a typo in the document
        // as it says the APR here will be 6.1.
        $this->calculate()->shouldReturn(6.0);
    }

    function it_calculates_ec_calculator_example_22_case_2_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1339.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1550.61,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.4);
    }

    function it_calculates_ec_calculator_example_23_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            240,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.4);
    }

    function it_calculates_ec_calculator_example_23_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1578.43,
            231,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.6);
    }

    // The calculation for Example 24 part 1 is same as for Example 23 part 1.

    function it_calculates_ec_calculator_example_24_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            60,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addInstalment(
            166909.73,
            Calculator::DAYS_IN_YEAR * 5
        );

        $this->calculate()->shouldReturn(5.6);
    }

    // The calculation for Example 25 is same as for Example 23 part 2.

    function it_calculates_ec_calculator_example_26_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1660.94,
            180,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.2);
    }

    function it_calculates_ec_calculator_example_26_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1660.94,
            6,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1734.38,
            174,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 7
        );

        $this->calculate()->shouldReturn(6.9);
    }

    function it_calculates_ec_calculator_example_27()
    {
        $this->beConstructedWith(170000);
        $this->addInstalment(3400, 0);

        $this->addRegularInstalments(
            1217.93,
            240,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.4);
    }

    function it_calculates_ec_calculator_example_28()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000 * 1.002, 0);

        $this->addRegularInstalments(
            round(1319.91 * 1.002, 2),
            240,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.4);
    }

    function it_calculates_ec_calculator_example_29()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000 * 1.002, 0);

        $this->addRegularInstalments(
            round(1349.91 * 1.002, 2),
            240,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.7);
    }

    function it_calculates_ec_calculator_example_30_part_1()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $this->addRegularInstalments(
            169.62,
            60,
            Calculator::FREQUENCY_MONTHLY
        );

        $owed = 30000;
        $principal = $owed / 120;
        for ($month = 61; $month <= 180; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(7.3);
    }

    function it_calculates_ec_calculator_example_30_part_2()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $this->addRegularInstalments(
            169.62,
            6,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            202.09,
            54,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 7
        );

        $owed = 30000;
        $principal = $owed / 120;

        for ($month = 61; $month <= 180; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(8.6);
    }

    function it_calculates_ec_calculator_example_31_part_1()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $owed = 30000;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.2);
    }

    function it_calculates_ec_calculator_example_31_part_2()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $owed = 30000;
        $principal = $owed / 12;

        for ($month = 1; $month <= 6; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.5);
    }

    function it_calculates_ec_calculator_example_32_part_1()
    {
        $this->beConstructedWith(7500);
        $this->addInstalment(600, 0);

        $owed = 7500;
        $principal = 7500 / 12;

        for ($month = 1; $month <= 3; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            7500,
            Calculator::FREQUENCY_MONTHLY * 3,
            Calculator::INSTALMENT_TYPE_ADVANCE
        );

        $owed += 7500;
        $principal += 7500 / 9;

        for ($month = 4; $month <= 6; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            15000,
            Calculator::FREQUENCY_MONTHLY * 6,
            Calculator::INSTALMENT_TYPE_ADVANCE
        );

        $owed += 15000;
        $principal += 15000 / 6;

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(13.1);
    }

    function it_calculates_ec_calculator_example_32_part_2()
    {
        $this->beConstructedWith(7500);
        $this->addInstalment(600, 0);

        $owed = 7500;
        $principal = 7500 / 12;

        for ($month = 1; $month <= 3; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            7500,
            Calculator::FREQUENCY_MONTHLY * 3,
            Calculator::INSTALMENT_TYPE_ADVANCE
        );

        $owed += 7500;
        $principal += 7500 / 9;

        for ($month = 4; $month <= 6; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            15000,
            Calculator::FREQUENCY_MONTHLY * 6,
            Calculator::INSTALMENT_TYPE_ADVANCE
        );

        $owed += 15000;
        $principal += 15000 / 6;

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(13.9);
    }

    // The calculation for Example 33 part 1 is explained to be same as for Example 31 part 1.

    function it_calculates_ec_calculator_example_33_part_2()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $owed = 30000;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(12.6);
    }

    function it_calculates_ec_calculator_example_34()
    {
        $this->beConstructedWith(1500);
        $this->addInstalment(30, 0);

        $owed = 1500;
        $principal = $owed / 9;

        for ($month = 1; $month <= 9; $month++) {
            $interest = $owed * (pow(1.075, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            1500,
            Calculator::FREQUENCY_MONTHLY * 9,
            Calculator::INSTALMENT_TYPE_ADVANCE
        );
        $owed += 1500;
        $principal = $owed / 3;

        for ($month = 10; $month <= 12; $month++) {
            $interest = $owed * (pow(1.075, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.4);
    }

    function it_calculates_ec_example_35()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(100, Calculator::FREQUENCY_MONTHLY * 3);
        $this->addInstalment(3000, Calculator::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(5.6);
    }

    function it_calculates_ec_example_36()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(25, Calculator::FREQUENCY_MONTHLY);

        $owed = 3000;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.09, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(15.1);
    }

    function it_calculates_ec_example_37()
    {
        $this->beConstructedWith(1500);
        $this->addInstalment(30, 0);
        $this->addInstalment(25, Calculator::FREQUENCY_MONTHLY);

        $owed = 1500;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.09, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Calculator::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(17.0);
    }

    function it_calculates_ec_example_38()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addRegularInstalments(
            24.12,
            3,
            Calculator::FREQUENCY_MONTHLY
        );
        $this->addInstalment(3000, Calculator::FREQUENCY_MONTHLY * 3);

        $this->calculate()->shouldReturn(19.4);
    }

    function it_calculates_ec_example_39()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(
            3132.09,
            Calculator::FREQUENCY_MONTHLY * 6
        );

        $this->calculate()->shouldReturn(13.5);
    }

    function it_calculates_ec_example_40()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addInstalment(207618.17, Calculator::FREQUENCY_MONTHLY * 6);

        $this->calculate()->shouldReturn(12.2);
    }

    function it_calculates_ec_example_41()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addInstalment(7500, 0);
        $this->addInstalment(200000, Calculator::FREQUENCY_MONTHLY * 6);

        $this->calculate()->shouldReturn(12.6);
    }

    function it_calculates_ec_example_42()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(
            1250,
            12,
            Calculator::FREQUENCY_MONTHLY
        );
        $this->addInstalment(200000, Calculator::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(10.0);
    }

    function it_calculates_ec_example_43()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(150, -Calculator::DAYS_IN_YEAR);
        $this->addInstalment(450, 0);
        $this->addRegularInstalments(
            356.11,
            120,
            Calculator::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(8.3);
    }

    function it_calculates_ec_example_44()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(
            1111.11,
            60,
            Calculator::FREQUENCY_MONTHLY
        );
        $this->addRegularInstalments(
            1349.94,
            120,
            Calculator::FREQUENCY_MONTHLY,
            Calculator::FREQUENCY_MONTHLY * 61
        );
        $this->addInstalment(32075.08, Calculator::FREQUENCY_MONTHLY * 180);

        $this->calculate()->shouldReturn(3.5);
    }
}
