<?php

namespace spec\RQ\APRCalculator;

use RQ\APRCalculator\Calculator;
use RQ\APRCalculator\Instalment;
use PhpSpec\Exception\Example\SkippingException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class CalculatorSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(100); // Amount of first advance
    }

    /**
     * The examples below are ported directly from the C# library tests.
     *
     * @return void
     */
    function it_is_initializable()
    {
        $this->shouldHaveType(Calculator::class);
    }

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
        $this->addRegularInstalments(222.44, 60, Instalment::FREQUENCY_MONTHLY);

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
        $this->addRegularInstalments(1432.86, 240, Instalment::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.434412);
    }

    function it_calculates_ec_calculator_example_2_case_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1433.57, 240, Instalment::FREQUENCY_MONTHLY, 3 + 31);

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.434185);
    }

    function it_calculates_ec_calculator_example_2_case_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1433.56, 240, Instalment::FREQUENCY_MONTHLY, 3 + 31);

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.434111);
    }

    function it_calculates_ec_calculator_example_2_case_3()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(16541.86, 20, Instalment::FREQUENCY_YEARLY, 3 + 31);

        $this->calculate()->shouldReturn(6.3);
        $this->calculate(0, 6)->shouldReturn(6.282070);
    }

    function it_calculates_ec_calculator_example_3()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86 + round(200 / 12, 2), 240, Instalment::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(6.6);
        $this->calculate(0, 6)->shouldReturn(6.588554);
    }

    function it_calculates_ec_calculator_example_4()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86 + round(200000 / 100 / 12, 2), 240, Instalment::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(7.9);
        $this->calculate(0, 6)->shouldReturn(7.946625);
    }

    function it_calculates_ec_calculator_example_5()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1490.18, 240, Instalment::FREQUENCY_MONTHLY);

        $this->calculate()->shouldReturn(7.0);
        $this->calculate(0, 6)->shouldReturn(6.961575);
    }

    function it_calculates_ec_calculator_example_6()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1432.86, 240, Instalment::FREQUENCY_MONTHLY);
        $this->addInstalment(100, Instalment::DAYS_IN_YEAR * 20);

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.436359);
    }

    function it_calculates_ec_calculator_example_7()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1199.10, 180, Instalment::FREQUENCY_MONTHLY);
        $this->addInstalment(142097.69, 15 * Instalment::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.409523);
    }

    function it_calculates_ec_calculator_example_8_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1166.67, 240, Instalment::FREQUENCY_MONTHLY);
        $this->addInstalment(200000, 20 * Instalment::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(7.4);
        $this->calculate(0, 6)->shouldReturn(7.430479);
    }

    function it_calculates_ec_calculator_example_8_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1166.67, 6, Instalment::FREQUENCY_MONTHLY);
        $this->addRegularInstalments(1398.33, 234, Instalment::FREQUENCY_MONTHLY, 7 / 12 * Instalment::DAYS_IN_YEAR);
        $this->addInstalment(200000, 20 * Instalment::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(8.9);
        $this->calculate(0, 6)->shouldReturn(8.869280);
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
                Instalment::FREQUENCY_MONTHLY,
                Instalment::DAYS_IN_YEAR / 12 + Instalment::DAYS_IN_YEAR * $year
            );

            $instalment = $instalment * 1.03;
        }

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.406400);
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
                Instalment::FREQUENCY_MONTHLY,
                Instalment::DAYS_IN_YEAR / 12 + Instalment::DAYS_IN_YEAR * $year
            );
            $instalment = $instalment * 0.97;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.468360);
    }

    function it_calculates_ec_calculator_example_11()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(1500, 220, Instalment::FREQUENCY_MONTHLY);
        $this->addInstalment(407.70, Instalment::DAYS_IN_YEAR / 12 * 221);

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.452756);
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
                Instalment::DAYS_IN_YEAR / 12 * $month++
            );

            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.492533);
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
                Instalment::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.476009);
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
                Instalment::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.8);
        $this->calculate(0, 6)->shouldReturn(6.818859);
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
                Instalment::DAYS_IN_YEAR / 12 * ++$month
            );
            $owed -= $repayment;
        }

        $this->calculate()->shouldReturn(6.7);
        $this->calculate(0, 6)->shouldReturn(6.695965);
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
                Instalment::DAYS_IN_YEAR / 12 * $month
            );
            $owed -= $repayment;
        }

        $interest = $owed * 0.06 / 12;
        $this->addInstalment(
            round($owed + $interest, 2),
            Instalment::DAYS_IN_YEAR / 12 * $month
        );

        $this->calculate()->shouldReturn(6.8);
        $this->calculate(0, 6)->shouldReturn(6.822923);
    }

    function it_calculates_ec_calculator_example_18_case_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;

        $this->addRegularInstalments(
            1429.01,
            240,
            Instalment::FREQUENCY_MONTHLY,
            14
        );

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.435937);
    }

    function it_calculates_ec_calculator_example_18_case_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;

        $this->addRegularInstalments(
            1437.54,
            240,
            Instalment::FREQUENCY_MONTHLY,
            20 + Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.432478);
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
                Instalment::DAYS_IN_YEAR / 12 * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.523259);
    }

    function it_calculates_ec_calculator_example_20()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            24,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1423.41,
            216,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 25
        );

        $this->calculate()->shouldReturn(6.2);
        $this->calculate(0, 6)->shouldReturn(6.190654);
    }

    function it_calculates_ec_calculator_example_21_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1374.06,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(5.9);
        $this->calculate(0, 6)->shouldReturn(5.853526);
    }

    function it_calculates_ec_calculator_example_21_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1530.61,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.2);
        $this->calculate(0, 6)->shouldReturn(7.199734);
    }

    function it_calculates_ec_calculator_example_22_case_1_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1349.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1404.06,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(6.1);
        $this->calculate(0, 6)->shouldReturn(6.134668);
    }

    function it_calculates_ec_calculator_example_22_case_1_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1349.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1515.81,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.1);
        $this->calculate(0, 6)->shouldReturn(7.093592);
    }

    function it_calculates_ec_calculator_example_22_case_2_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1339.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1394.06,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        // NOTE: there seems to be a typo in the document
        // as it says the APR here will be 6.1.
        $this->calculate()->shouldReturn(6.0);
        $this->calculate(0, 6)->shouldReturn(6.041228);
    }

    function it_calculates_ec_calculator_example_22_case_2_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1339.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1550.61,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.4);
        $this->calculate(0, 6)->shouldReturn(7.379073);
    }

    function it_calculates_ec_calculator_example_23_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            240,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.4);
        $this->calculate(0, 6)->shouldReturn(5.370286);
    }

    function it_calculates_ec_calculator_example_23_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            9,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1578.43,
            231,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 10
        );

        $this->calculate()->shouldReturn(7.6);
        $this->calculate(0, 6)->shouldReturn(7.597578);
    }

    // The calculation for Example 24 part 1 is same as for Example 23 part 1.

    function it_calculates_ec_calculator_example_24_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1319.91,
            60,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addInstalment(
            166909.73,
            Instalment::DAYS_IN_YEAR * 5
        );

        $this->calculate()->shouldReturn(5.6);
        $this->calculate(0, 6)->shouldReturn(5.635609);
    }

    // The calculation for Example 25 is same as for Example 23 part 2.

    function it_calculates_ec_calculator_example_26_part_1()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1660.94,
            180,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.2);
        $this->calculate(0, 6)->shouldReturn(6.237362);
    }

    function it_calculates_ec_calculator_example_26_part_2()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);

        $this->addRegularInstalments(
            1660.94,
            6,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            1734.38,
            174,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 7
        );

        $this->calculate()->shouldReturn(6.9);
        $this->calculate(0, 6)->shouldReturn(6.925014);
    }

    function it_calculates_ec_calculator_example_27()
    {
        $this->beConstructedWith(170000);
        $this->addInstalment(3400, 0);

        $this->addRegularInstalments(
            1217.93,
            240,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.434402);
    }

    function it_calculates_ec_calculator_example_28()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000 * 1.002, 0);

        $this->addRegularInstalments(
            round(1319.91 * 1.002, 2),
            240,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.4);
        $this->calculate(0, 6)->shouldReturn(5.396096);
    }

    function it_calculates_ec_calculator_example_29()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000 * 1.002, 0);

        $this->addRegularInstalments(
            round(1349.91 * 1.002, 2),
            240,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(5.7);
        $this->calculate(0, 6)->shouldReturn(5.682613);
    }

    function it_calculates_ec_calculator_example_30_part_1()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $this->addRegularInstalments(
            169.62,
            60,
            Instalment::FREQUENCY_MONTHLY
        );

        $owed = 30000;
        $principal = $owed / 120;
        for ($month = 61; $month <= 180; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(7.3);
        $this->calculate(0, 6)->shouldReturn(7.302956);
    }

    function it_calculates_ec_calculator_example_30_part_2()
    {
        $this->beConstructedWith(30000);
        $this->addInstalment(600, 0);

        $this->addRegularInstalments(
            169.62,
            6,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->addRegularInstalments(
            202.09,
            54,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 7
        );

        $owed = 30000;
        $principal = $owed / 120;

        for ($month = 61; $month <= 180; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(8.6);
        $this->calculate(0, 6)->shouldReturn(8.61132);
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
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.2);
        $this->calculate(0, 6)->shouldReturn(11.164789);
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
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.5);
        $this->calculate(0, 6)->shouldReturn(11.542158);
    }

    function it_calculates_ec_calculator_example_32_part_1()
    {
        $this->beConstructedWith(7500);

        $owed = 7500;
        $principal = 7500 / 12;

        for ($month = 1; $month <= 3; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            7500,
            Instalment::FREQUENCY_MONTHLY * 3,
            Instalment::TYPE_ADVANCE
        );

        $owed += 7500;
        $principal += 7500 / 9;

        for ($month = 4; $month <= 6; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            15000,
            Instalment::FREQUENCY_MONTHLY * 6,
            Instalment::TYPE_ADVANCE
        );

        $owed += 15000;
        $principal += 15000 / 6;

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(13.1);
        $this->calculate(0, 6)->shouldReturn(13.063818);
    }

    function it_calculates_ec_calculator_example_32_part_2()
    {
        $this->beConstructedWith(7500);

        $owed = 7500;
        $principal = 7500 / 12;

        for ($month = 1; $month <= 3; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            7500,
            Instalment::FREQUENCY_MONTHLY * 3,
            Instalment::TYPE_ADVANCE
        );

        $owed += 7500;
        $principal += 7500 / 9;

        for ($month = 4; $month <= 6; $month++) {
            $interest = $owed * (pow(1.07, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            15000,
            Instalment::FREQUENCY_MONTHLY * 6,
            Instalment::TYPE_ADVANCE
        );

        $owed += 15000;
        $principal += 15000 / 6;

        for ($month = 7; $month <= 12; $month++) {
            $interest = $owed * (pow(1.0839, 1 / 12) - 1);
            // echo round($principal + $interest, 2) . PHP_EOL;
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(13.9);
        $this->calculate(0, 6)->shouldReturn(13.945824);
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
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(12.6);
        // The document says this is 12.625685, but it seems our
        // calculations are even more precise than those in the document.
        $this->calculate(0, 6)->shouldReturn(12.625687);
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
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->addInstalment(
            1500,
            Instalment::FREQUENCY_MONTHLY * 9,
            Instalment::TYPE_ADVANCE
        );
        $owed += 1500;
        $principal = $owed / 3;

        for ($month = 10; $month <= 12; $month++) {
            $interest = $owed * (pow(1.075, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(11.4);
        // Again, the document specifies 11.415822, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(11.415821);
    }

    function it_calculates_ec_example_35()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(100, Instalment::FREQUENCY_MONTHLY * 3);
        $this->addInstalment(3000, Instalment::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(5.6);
        // Again, the document specifies 5.583621, but our calculation is more precise.
        $this->calculate(0, 6)->shouldReturn(5.583645);
    }

    function it_calculates_ec_example_36()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(25, Instalment::FREQUENCY_MONTHLY);

        $owed = 3000;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.09, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(15.1);
        // Again, the document specifies 15.10627, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(15.10631);
    }

    function it_calculates_ec_example_37()
    {
        $this->beConstructedWith(1500);
        $this->addInstalment(30, 0);
        $this->addInstalment(25, Instalment::FREQUENCY_MONTHLY);

        $owed = 1500;
        $principal = $owed / 12;

        for ($month = 1; $month <= 12; $month++) {
            $interest = $owed * (pow(1.09, 1/12) - 1);
            $this->addInstalment(
                round($principal + $interest, 2),
                Instalment::FREQUENCY_MONTHLY * $month
            );
            $owed -= $principal;
        }

        $this->calculate()->shouldReturn(17.0);
        // Again, the document specifies 16.991403, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(16.991553);
    }

    function it_calculates_ec_example_38()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addRegularInstalments(
            24.12,
            3,
            Instalment::FREQUENCY_MONTHLY
        );
        $this->addInstalment(3000, Instalment::FREQUENCY_MONTHLY * 3);

        $this->calculate()->shouldReturn(19.4);
        // Again, the document specifies 19.429412, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(19.429575);
    }

    function it_calculates_ec_example_39()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(60, 0);
        $this->addInstalment(
            3132.09,
            Instalment::FREQUENCY_MONTHLY * 6
        );

        $this->calculate()->shouldReturn(13.5);
        // Again, the document specifies 13.494231, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(13.494236);
    }

    function it_calculates_ec_example_40()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addInstalment(207618.17, Instalment::FREQUENCY_MONTHLY * 6);

        $this->calculate()->shouldReturn(12.2);
        $this->calculate(0, 6)->shouldReturn(12.206644);
    }

    function it_calculates_ec_example_41()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addInstalment(7500, 0);
        $this->addInstalment(200000, Instalment::FREQUENCY_MONTHLY * 6);

        $this->calculate()->shouldReturn(12.6);
        // Again, the document specifies 12.573788, but this is close enough to just assume
        // a difference in precision of calculations.
        $this->calculate(0, 6)->shouldReturn(12.573789);
    }

    function it_calculates_ec_example_42()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(
            1250,
            12,
            Instalment::FREQUENCY_MONTHLY
        );
        $this->addInstalment(200000, Instalment::DAYS_IN_YEAR);

        $this->calculate()->shouldReturn(10.0);
        $this->calculate(0, 6)->shouldReturn(10.039962);
    }

    function it_calculates_ec_example_43()
    {
        $this->beConstructedWith(3000);
        $this->addInstalment(150, -Instalment::DAYS_IN_YEAR);
        $this->addInstalment(450, 0);
        $this->addRegularInstalments(
            356.11,
            120,
            Instalment::FREQUENCY_MONTHLY
        );

        $this->calculate()->shouldReturn(8.3);
        $this->calculate(0, 6)->shouldReturn(8.269278);
    }

    function it_calculates_ec_example_44()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $this->addRegularInstalments(
            1111.11,
            60,
            Instalment::FREQUENCY_MONTHLY
        );
        $this->addRegularInstalments(
            1349.94,
            120,
            Instalment::FREQUENCY_MONTHLY,
            Instalment::FREQUENCY_MONTHLY * 61
        );
        $this->addInstalment(32075.08, Instalment::FREQUENCY_MONTHLY * 180);

        $this->calculate()->shouldReturn(3.5);
        $this->calculate(0, 6)->shouldReturn(3.470057);
    }
}
