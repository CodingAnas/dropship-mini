<?php

namespace App\Tests\Service;

use PHPUnit\Framework\TestCase;
use App\Service\PriceCalculator;
use Override;

class PriceCalculatorTest extends TestCase {

    private PriceCalculator $pr;

    protected function setUp(): void
    {
        $this->pr = new PriceCalculator(50.0);
    }

    function testZeroCase() : void {
        $price = $this->pr->withMarkup(0.0, 0.0);
        $this->assertEquals(0.0, $price);
    }

    function testActualResult() : void {
        $price = $this->pr->withMarkup(10.0, 50.0);
        $this->assertEquals(15.0, $price);
    }
}