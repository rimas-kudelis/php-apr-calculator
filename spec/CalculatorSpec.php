<?php

namespace spec\RQ\APRCCalculator;

use RQ\APRCCalculator\Calculator;
use RQ\APRCCalculator\Instalment;
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
}
