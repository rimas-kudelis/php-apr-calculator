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

    public function calculateForSinglePayment(float $payment, int $daysAfterAdvance, int $round = self::DEFAULT_PRECISION): float
    {
        return round(
            (
                pow(
                    $this->advances[0]->getAmount() / $payment,
                    (-Instalment::DAYS_IN_YEAR / $daysAfterAdvance)
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

    public function addInstalment(
        float $amount,
        float $daysAfterFirstAdvance,
        int $type = Instalment::TYPE_PAYMENT
    ): void {
        $instalment = new Instalment($amount, $daysAfterFirstAdvance);

        if ($type === Instalment::TYPE_PAYMENT) {
            $this->payments[] = $instalment;
        } elseif ($type === Instalment::TYPE_ADVANCE) {
            $this->advances[] = $instalment;
        } else {
            throw new DomainException('Invalid instalment type!');
        }
    }

    public function addRegularInstalments(
        float $amount,
        float $numberOfInstalments,
        float $daysBetweenAdvances,
        float $daysAfterFirstAdvance = 0,
        int $type = Instalment::TYPE_PAYMENT
    ): void {
        if ($daysAfterFirstAdvance === 0.0) {
            $daysAfterFirstAdvance = $daysBetweenAdvances;
        }

        for ($i = 0; $i < $numberOfInstalments; $i++) {
            $this->addInstalment($amount, $daysAfterFirstAdvance + $daysBetweenAdvances * $i);
        }
    }

    protected function calculateBetweenValues($lowRate, $highRate): float
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

    protected function calculateEquationErrorForRate($rate): float
    {
        $advancesComponent = $paymentsComponent = 0;

        foreach ($this->advances as $advance) {
            $advancesComponent += $advance->calculate($rate);
        }

        foreach ($this->payments as $payment) {
            $paymentsComponent += $payment->calculate($rate);
        }

        return $paymentsComponent - $advancesComponent;
    }
}
