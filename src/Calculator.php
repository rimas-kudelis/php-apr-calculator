<?php

namespace RQ\APRCCalculator;

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
    const ROUND_PRECISION = 1;
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

    public function calculateForSinglePayment(float $payment, int $daysAfterAdvance): float
    {
        return round(
            (
                pow(
                    $this->advances[0]->getAmount() / $payment,
                    (-Instalment::DAYS_IN_YEAR / $daysAfterAdvance)
                ) - 1
            ) * 100,
            self::ROUND_PRECISION
        );
    }

    public function calculate(float $guess = 0): float
    {
        $rateToTry = $guess / 100;
        $difference = 1;
        $amountToAdd = self::INITIAL_GUESS_INCREMENT;

        while (true) {
            $advances = $payments = 0;

            foreach ($this->advances as $advance) {
                $advances += $advance->calculate($rateToTry);
            }

            foreach ($this->payments as $payment) {
                $payments += $payment->calculate($rateToTry);
            }

            $difference = $payments - $advances;

            if (abs($difference) <= self::FINANCE_PRECISION) {
                break;
            }

            if ($difference > 0) {
                $amountToAdd = $amountToAdd * 2;
                $rateToTry = $rateToTry + $amountToAdd;
            } else {
                $amountToAdd = $amountToAdd / 2;
                $rateToTry = $rateToTry - $amountToAdd;
            }
        }

        return round($rateToTry * 100, self::ROUND_PRECISION);
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
}
