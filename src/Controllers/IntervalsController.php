<?php 
declare(strict_types=1);

namespace App\Controllers;
use App\Database\DB;
use App\Services\IntervalsService;

class IntervalsController{
    private $intervalsService;

    /**
     * contruct injects intervals service.
     */
    function __construct(){
        $this->intervalsService = new IntervalsService();
    }

    /**
     * Get all intervals in the database.
    * @return string
    */
    public function getAllIntervals(): String
    {
        header('Content-Type: application/json');
        return json_encode($this->intervalsService->getIntervals());   
    }

    /**
     * Delete all data on intervals table.
     * @return string
     */
    public function deleteAll(): String {
        header('Content-Type: application/json');
        return json_encode($this->intervalsService->clearTable());   
    }

    /**
     * Add intervals to the collection and saves to database.
     * @return string
     */
    public function createIntervals($intervalParameters): String {
        header('Content-Type: application/json');
        $intervalParameters = json_decode($intervalParameters);
        $interval = explode('-', $intervalParameters->add);
        $startDate = date('Y-m-d',strtotime('now'));
        $endDate = date('Y-m-d',strtotime('now'));
        $price = 0;
        switch(count($interval)){
            case 2:
            //1-4:15
            $startDay = $interval[0];
            $price = explode(':', $interval[1]);
            $endDay = $price[0];
            $price = $price[1];
            $startDate = date(sprintf('Y-m-%02d', $startDay),strtotime('now'));
            $endDate = date(sprintf('Y-m-%02d', $endDay),strtotime('now'));
            $price = $price;
            break;

            case 4:
            //04-01-04-10:15
            $startDay = $interval[1];
            $startMonth = $interval[0];
            $price = explode(':', $interval[3]);
            $endDay = $price[0];
            $endMonth = $interval[2];
            $price = $price[1];
            $startDate = date(sprintf('Y-%02d-%02d', $startMonth, $startDay),strtotime('now'));
            $endDate = date(sprintf('Y-%02d-%02d', $endMonth, $endDay),strtotime('now'));
            $price = $price;
            break;

            case 6:
            //2019-04-01-2019-04-10:15
            $startDay = $interval[2];
            $startMonth = $interval[1];
            $startYear = $interval[0];
            $price = explode(':', $interval[5]);
            $endDay = $price[0];
            $endMonth = $interval[4];
            $endYear = $interval[3];
            $price = $price[1];
            $startDate = date(sprintf('%02d-%02d-%02d', $startYear, $startMonth, $startDay));
            $endDate = date(sprintf('%02d-%02d-%02d', $endYear, $endMonth, $endDay));
            $price = $price;
            break;

            default:
            break;
        }
        $this->intervalsService->addInterval($startDate, $endDate, $price);
        $this->intervalsService->saveToDatabase();
        return json_encode($this->intervalsService->getIntervals());
    }
}