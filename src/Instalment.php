<?php

namespace RQ\APRCalculator;

/**
 * @copyright © 2014 Stephen Haunts
 * @copyright © 2019 Rimas Kudelis
 * @author Stephen Haunts http://www.stephenhaunts.com
 * @author Graham Johnson
 * @author Rimas Kudelis https://rimas.kudelis.lt
 * @license MIT
 * @link https://github.com/rimas-kudelis/php-apr-calculator
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
