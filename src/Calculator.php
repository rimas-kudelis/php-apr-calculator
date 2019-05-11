<?php

namespace RQ\APRCalculator;

use DomainException;

/**
 * APR Calculator : Example of FCA Compliant APR Calculator.
 *                  Complies with FCA MBOC 10.3 Formular for calculating APR
 *                  http://fshandbook.info/FS/html/FCA/MCOB/10/3
 *
 * Copyright (C) 2014 Stephen Haunts
 * http://www.stephenhaunts.com
 *
 * PHP port Copyright (C) 2019 Rimas Kudelis
 * https://github.com/rimas-kudelis
 *
 * This file is part of APR Calculator.
 *
 * APR Calculator is free software: you can redistribute it and/or modify it under the terms of the
 * GNU General Public License as published by the Free Software Foundation, either version 2 of the
 * License, or (at your option) any later version.
 *
 * APR Calculator is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 *
 * See the GNU General Public License for more details <http://www.gnu.org/licenses/>.
 *
 * Authors: Stephen Haunts, Graham Johnson, Rimas Kudelis
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

    public function calculateForSinglePayment(float $payment, int $daysAfterAdvance, int $round = self::DEFAULT_PRECISION): float
    {
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
            $advancesComponent += self::calculateSummand($advance, $rate);
        }

        foreach ($this->payments as $payment) {
            $paymentsComponent += self::calculateSummand($payment, $rate);
        }

        return $paymentsComponent - $advancesComponent;
    }

    protected static function calculateSummand(Instalment $instalment, float $rate): float
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
