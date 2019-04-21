<?php
namespace App\Services;
use App\Database\DB;

class IntervalsService
{
    private $intervals = [];
    private $DB;

    /**
     * Constructor method injects $DB class.
     */
    function __construct(){
        $this->DB = new DB();
    }

    /**
     * This method fills empty indexes with -1 to work with second case
     * @param $ranges
     * @return array
     */
    private function fillEmptySpaces($ranges)
    {
        ksort($ranges);
        $first = key(array_slice($ranges, 0, 1, TRUE));
        $last = key(array_slice($ranges, -1, 1, TRUE));
        while (true) {
            $first = date('Y-m-d', strtotime($first . ' +1 day'));
            if ($first == $last) {
                break;
            }
            if (!isset($ranges[$first])) {
                $ranges[$first] = -1;
            }
        }
        ksort($ranges);
        return $ranges;
    }

    /**
     * This method will add a new record to the intervals array and recalculates new intervals
     * @param $dateStart
     * @param $dateEnd
     * @param $price
     * @return array
     */
    public function addInterval($dateStart, $dateEnd, $price)
    {
        $ranges = $this->getCurrentRanges();
        $period = new \DatePeriod(
            new \DateTime($dateStart),
            new \DateInterval('P1D'),
            new \DateTime($dateEnd. '+1 day')
        );
        foreach($period as $key => $value){
            $ranges[$value->format('Y-m-d')] = $price;
        }
        $ranges = $this->fillEmptySpaces($ranges);
        $start = key(array_slice($ranges, 0, 1, TRUE));
        $last = key(array_slice($ranges, -1, 1, TRUE));
        $newIntervals = [];

        foreach ($ranges as $index => $value) {
            if ($value == -1) {
                $start = date('Y-m-d', strtotime($start . ' +1 day'));
                continue;
            }
            if ($index == $last) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
            } else if (isset($ranges[date('Y-m-d', strtotime($index . ' +1 day'))]) && $value !== $ranges[date('Y-m-d', strtotime($index . ' +1 day'))]) {
                $newIntervals[] = ['date_start' => $start, 'date_end' => $index, 'price' => $value];
                $start = date('Y-m-d', strtotime($index . ' +1 day'));
            }
        }
        $this->intervals = $newIntervals;
        return $this->intervals;
    }

    /**
     * Converts current intervals to an array with ranges
     * @return array
     */
    private function getCurrentRanges()
    {
        $array = [];
        $this->getIntervals();
        $currentRecords = $this->intervals;
        foreach ($currentRecords as $record) {
            $dateStart = date($record['date_start']);
            $dateEnd = date($record['date_end']);
            $period = new \DatePeriod(
                new \DateTime($dateStart),
                new \DateInterval('P1D'),
                new \DateTime($dateEnd . '+1 day')
           );
           foreach($period as $key => $value){
               $array[$value->format('Y-m-d')] = $record['price']; 
           }
        }
        return $array;
    }

    /**
     * @return array
     */
    public function getIntervals()
    {
        $this->readFromDatabase();
        return $this->intervals;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        $string = "";
        foreach ($this->intervals as $interval) {
            $string .= "({$interval['date_start']}-{$interval['date_end']}:{$interval['price']}), ";
        }
        return rtrim($string, ', ');
    }

    /**
     *
     */
    public function saveToDatabase()
    {
        $this->clearTable();
        $inserStatement = "INSERT INTO intervalChallenge.intervals (date_start, date_end, price) VALUES ";
        $values = [];
        foreach ($this->intervals as $interval) {
            $values[] = sprintf("('%s', '%s', %f)", $interval['date_start'], $interval['date_end'], $interval['price']);
        }
        $inserStatement .= join($values, ',');
        $this->DB->Query($inserStatement, []);
    }

    public function clearTable()
    {
        $this->DB->Query("TRUNCATE TABLE intervalChallenge.intervals", []);
    }

    /**
     * Read the records from the database and maps returning an array.
     */
    public function readFromDatabase()
    {
        $intervals = $this->DB->Query("SELECT date_start, date_end, price FROM intervalChallenge.intervals", []);
        // You have to map results to the intervals variable:
        if($intervals){
            $this->intervals = array_map(function($result){
                return [
                    'date_start' => $result->date_start, 
                    'date_end' => $result->date_end, 
                    'price' => $result->price
                ];
            }, $intervals);
        }else{
            $this->intervals = [];
        }
    }

}
