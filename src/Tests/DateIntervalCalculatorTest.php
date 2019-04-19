<?php

use PHPUnit\Framework\TestCase;

require_once "DateIntervalCalculator.php";

class DateIntervalCalculatorTest extends TestCase
{

    public function testFirstCase()
    {
        $calculator = new DateIntervalCalculator();

        $calculator->addInterval(1, 10, 15);
        $this->assertEquals('(1-10:15)', $calculator);

        $calculator->addInterval(5, 20, 15);
        $this->assertEquals('(1-20:15)', $calculator);

        $calculator->addInterval(2, 8, 45);
        $this->assertEquals('(1-1:15), (2-8:45), (9-20:15)', $calculator);

        $calculator->addInterval(9, 10, 45);
        $this->assertEquals('(1-1:15), (2-10:45), (11-20:15)', $calculator);

        $calculator->saveToDatabase();


    }

    public function testSecondCase()
    {
        $calculator = new DateIntervalCalculator();

        $calculator->addInterval(1, 5, 15);
        $this->assertEquals('(1-5:15)', $calculator);

        $calculator->addInterval(20, 25, 15);
        $this->assertEquals('(1-5:15), (20-25:15)', $calculator);

        $calculator->addInterval(4, 21, 45);
        $this->assertEquals('(1-3:15), (4-21:45), (22-25:15)', $calculator);

        $calculator->addInterval(3, 21, 15);
        $this->assertEquals('(1-25:15)', strval($calculator));

    }

}