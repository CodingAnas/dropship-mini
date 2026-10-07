<?php 

namespace App\Service;

use InvalidArgumentException;

class PriceCalculator {

    public function __construct(private float $defaultMarkupPercent = 20.0) {}

    public function withMarkup(float $cost, ?float $percent = null) : float {

        if ($cost < 0) { throw new \InvalidArgumentException('Cost cannot be negative'); }
        $percent ??= $this->defaultMarkupPercent;
        return round($cost * (1 + $percent / 100), 2);
    }
}