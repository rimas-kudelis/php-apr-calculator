<?php

namespace spec\RQ\APRCCalculator;

use RQ\APRCCalculator\Calculator;
use RQ\APRCCalculator\Instalment;
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
        $instalmentAmount = 1130.33;
        for ($year = 0; $year < 20; $year++) {
            $this->addRegularInstalments(
                round($instalmentAmount, 2),
                12,
                Instalment::FREQUENCY_MONTHLY,
                Instalment::DAYS_IN_YEAR / 12 + Instalment::DAYS_IN_YEAR * $year
            );
            $instalmentAmount = $instalmentAmount * 1.03;
        }

        $this->calculate()->shouldReturn(6.4);
        $this->calculate(0, 6)->shouldReturn(6.406400);
    }

    function it_calculates_ec_calculator_example_10()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $instalmentAmount = 1778.58;
        for ($year = 0; $year < 20; $year++) {
            $this->addRegularInstalments(
                // It seems the EC-supplied calculation works by rounding the instalment amount
                // at pay time, not at calculation time.
                round($instalmentAmount, 2),
                12,
                Instalment::FREQUENCY_MONTHLY,
                Instalment::DAYS_IN_YEAR / 12 + Instalment::DAYS_IN_YEAR * $year
            );
            $instalmentAmount = $instalmentAmount * 0.97;
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
            $instalment = min(900, $owed);
            $this->addInstalment(
                $instalment + round($owed * 0.06 / 12, 2),
                Instalment::DAYS_IN_YEAR / 12 * $month++
            );
            $owed -= $instalment;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.492533);
    }

    function it_calculates_ec_calculator_example_13()
    {
        $this->beConstructedWith(200000);
        $this->addInstalment(4000, 0);
        $owed = 200000;
        $month = 1;
        while ($owed > 0) {
            $instalment = min(200000 / 240, $owed);
            $this->addInstalment(
                round($instalment + $owed * 0.06 / 12, 2),
                Instalment::DAYS_IN_YEAR / 12 * $month++
            );
            $owed -= $instalment;
        }

        $this->calculate()->shouldReturn(6.5);
        $this->calculate(0, 6)->shouldReturn(6.476009);
    }
}
