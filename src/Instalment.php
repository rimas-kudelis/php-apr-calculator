<?php

namespace RQ\APRCalculator;

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
class Instalment
{
    /** @var float */
    protected $amount;

    /** @var float */
    protected $daysAfterFirstAdvance;

    public function __construct(float $amount, float $daysAfterFirstAdvance)
    {
        $this->amount = $amount;
        $this->daysAfterFirstAdvance = $daysAfterFirstAdvance;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getDaysAfterFirstAdvance(): float
    {
        return $this->daysAfterFirstAdvance;
    }

    public function setDaysAfterFirstAdvance(float $daysAfterFirstAdvance): self
    {
        $this->daysAfterFirstAdvance = $daysAfterFirstAdvance;

        return $this;
    }
}
