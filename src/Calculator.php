<?php

namespace RQ\APRCalculator;

use DomainException;

/**
 * @copyright © 2014 Stephen Haunts
 * @copyright © 2019 Rimas Kudelis
 * @author Stephen Haunts http://www.stephenhaunts.com
 * @author Graham Johnson
 * @author Rimas Kudelis https://rimas.kudelis.lt
 * @license MIT
 * @link https://github.com/rimas-kudelis/php-apr-calculator
 */
class Calculator
{
    const DAYS_IN_YEAR = 365.25;

    const FREQUENCY_DAILY = 1;
    const FREQUENCY_WEEKLY = 7;
    const FREQUENCY_FORTNIGHTLY = 14;
    const FREQUENCY_FOUR_WEEKLY = 28;
    const FREQUENCY_MONTHLY = self::DAYS_IN_YEAR / 12;
    const FREQUENCY_QUARTERLY = self::DAYS_IN_YEAR / 4;
    const FREQUENCY_YEARLY = self::DAYS_IN_YEAR;

    const INSTALMENT_TYPE_PAYMENT = 0;
    const INSTALMENT_TYPE_ADVANCE = 1;

    const DEFAULT_PRECISION = 1;
    const INITIAL_GUESS_INCREMENT = 0.0001;
    const FINANCE_PRECISION = 0.0000001;

    /** @var Instalment[] */
    protected $advances;

    /** @var Instalment[] */
    protected $payments;

    public function __construct(float $firstAdvance)
    {
        $this->advances[] = new Instalment($firstAdvance, 0);
    }

    public static function create(float $firstAdvance): self
    {
        return new static($firstAdvance);
    }

    public function calculateForSinglePayment(
        float $payment,
        float $daysAfterAdvance,
        int $round = self::DEFAULT_PRECISION
    ): float {
        return round(
            (
                pow(
                    $this->advances[0]->getAmount() / $payment,
                    (-self::DAYS_IN_YEAR / $daysAfterAdvance)
                ) - 1
            ) * 100,
            $round
        );
    }

    public function calculate(float $guess = 0, int $round = self::DEFAULT_PRECISION): float
    {
        $rateToTry = $guess / 100;
        $previousTriedRate = 0;
        $increment = self::INITIAL_GUESS_INCREMENT;

        while (true) {
            $error = $this->calculateEquationErrorForRate($rateToTry);

            if (abs($error) <= self::FINANCE_PRECISION) {
                break;
            }

            if ($error > 0) {
                $increment = $increment * 2;
                $previousTriedRate = $rateToTry;
                $rateToTry = $rateToTry + $increment;
            } else {
                $rateToTry = $this->calculateBetweenValues($previousTriedRate, $rateToTry);
                break;
            }
        }

        return round($rateToTry * 100, $round);
    }

    public function addAdvance(float $amount, float $daysAfterFirstAdvance): self
    {
        return $this->addInstalment($amount, $daysAfterFirstAdvance, self::INSTALMENT_TYPE_ADVANCE);
    }

    public function addPayment(float $amount, float $daysAfterFirstAdvance): self
    {
        return $this->addInstalment($amount, $daysAfterFirstAdvance, self::INSTALMENT_TYPE_PAYMENT);
    }

    public function addInstalment(
        float $amount,
        float $daysAfterFirstAdvance,
        int $type = self::INSTALMENT_TYPE_PAYMENT
    ): self {
        if ($type === self::INSTALMENT_TYPE_ADVANCE) {
            $this->advances[] = new Instalment($amount, $daysAfterFirstAdvance);
        } elseif ($type === self::INSTALMENT_TYPE_PAYMENT) {
            $this->payments[] = new Instalment($amount, $daysAfterFirstAdvance);
        } else {
            throw new DomainException('Invalid instalment type!');
        }

        return $this;
    }

    public function addRegularAdvances(
        float $amount,
        float $numberOfAdvances,
        float $daysBetweenAdvances,
        float $daysAfterFirstAdvance = 0
    ): self {
        return $this->addRegularInstalments(
            $amount,
            $numberOfAdvances,
            $daysBetweenAdvances,
            $daysAfterFirstAdvance,
            self::INSTALMENT_TYPE_ADVANCE
        );
    }

    public function addRegularPayments(
        float $amount,
        float $numberOfPayments,
        float $daysBetweenPayments,
        float $daysAfterFirstAdvance = 0
    ): self {
        return $this->addRegularInstalments(
            $amount,
            $numberOfPayments,
            $daysBetweenPayments,
            $daysAfterFirstAdvance,
            self::INSTALMENT_TYPE_PAYMENT
        );
    }

    public function addRegularInstalments(
        float $amount,
        float $numberOfInstalments,
        float $daysBetweenInstalments,
        float $daysAfterFirstAdvance = 0,
        int $type = self::INSTALMENT_TYPE_PAYMENT
    ): self {
        if ($daysAfterFirstAdvance === 0.0) {
            $daysAfterFirstAdvance = $daysBetweenInstalments;
        }

        for ($i = 0; $i < $numberOfInstalments; $i++) {
            $this->addInstalment($amount, $daysAfterFirstAdvance + $daysBetweenInstalments * $i, $type);
        }

        return $this;
    }

    protected function calculateBetweenValues(float $lowRate, float $highRate): float
    {
        $rateToTry = ($lowRate + $highRate) / 2;
        $error = $this->calculateEquationErrorForRate($rateToTry);

        if (abs($error) <= static::FINANCE_PRECISION) {
            return $rateToTry;
        } elseif ($error < 0) {
            return $this->calculateBetweenValues($lowRate, $rateToTry);
        } else {
            return $this->calculateBetweenValues($rateToTry, $highRate);
        }
    }

    protected function calculateEquationErrorForRate(float $rate): float
    {
        $advancesComponent = $paymentsComponent = 0;

        foreach ($this->advances as $advance) {
            $advancesComponent += self::calculateOperandForInstalmentAndRate($advance, $rate);
        }

        foreach ($this->payments as $payment) {
            $paymentsComponent += self::calculateOperandForInstalmentAndRate($payment, $rate);
        }

        return $paymentsComponent - $advancesComponent;
    }

    protected static function calculateOperandForInstalmentAndRate(Instalment $instalment, float $rate): float
    {
        $divisor = pow(1 + $rate, self::daysToYears($instalment->getDaysAfterFirstAdvance()));
        $sum = $instalment->getAmount() / $divisor;

        return $sum;
    }

    protected static function daysToYears(float $days): float
    {
        return $days / self::DAYS_IN_YEAR;
    }
}
