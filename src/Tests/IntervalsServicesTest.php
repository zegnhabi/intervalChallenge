<?php

use PHPUnit\Framework\TestCase;
use App\Services\IntervalsService;

class IntervalsServicesTest extends TestCase
{

    public function testFirstCase()
    {
        $calculator = new IntervalsService();
        $calculator->clearTable();

        $calculator->addInterval('2019-04-1', '2019-04-10', 15);
        $this->assertEquals('2019-04-1-2019-04-10:15)', $calculator);

        $calculator->addInterval('2019-04-05', '2019-04-20', 15);
        $this->assertEquals('(2019-04-01-2019-04-20:15)', $calculator);

        $calculator->addInterval('2019-04-02', '2019-04-08', 45);
        $this->assertEquals('(2019-04-01-2019-04-01:15)', '(2019-04-02-2019-04-08:45)', '(2019-04-09-2019-04-20:15)', $calculator);

        $calculator->addInterval('2019-04-09', '2019-04-10', 45);
        $this->assertEquals('(2019-04-01-2019-04-01:15), (2019-04-02-2019-04-10:45), (2019-04-11-2019-04-20:15)', $calculator);

        $calculator->saveToDatabase();


    }

    public function testSecondCase()
    {
        $calculator = new IntervalsService();

        $calculator->clearTable();

        $calculator->addInterval('2019-04-01', '2019-04-05', 15);
        $this->assertEquals('(2019-04-01-2019-04-05:15)', $calculator);

        $calculator->addInterval('2019-04-20', '2019-04-25', 15);
        $this->assertEquals('(2019-04-01-2019-04-05:15), (2019-04-20-2019-04-25:15)', $calculator);

        $calculator->addInterval('2019-04-04', '2019-04-21', 45);
        $this->assertEquals('(2019-04-01-2019-04-03:15), (2019-04-04-2019-04-21:45), (2019-04-22-2019-04-25:15)', $calculator);

        $calculator->addInterval('2019-04-03', '2019-04-21', 15);
        $this->assertEquals('(2019-04-01-2019-04-25:15)', strval($calculator));

    }

}