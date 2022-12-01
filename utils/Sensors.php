<?php declare(strict_types=1);

namespace utils;
#implement functionality for HRZones and variable heartrate with time in
# each zone
final class Sensors
{
    private array $ranges;

    private array $HRzones;

    private int $time;
    function __construct(int $age, object $timeInHRzones)
    {
        $this->ranges = [
            "heartRate" => [
                "max" => 220 - $age,
                "min" => 30
            ],
            "RestHR" => [
                "max" => 100,
                "min" => 30
            ],
            "HRV" => [
                "max" => 160,
                "min" => 15
            ],
            "HRzones" => [
                "primaryZone" => [
                    "start" => 50,
                    "end" => 60
                ],
                "secondaryZone" => [
                    "start" => 60,
                    "end" => 70
                ],
                "tertiaryZone" => [
                    "start" => 70,
                    "end" => 80
                ],
                "quaternaryZone" => [
                    "start" => 80,
                    "end" => 90,
                ],
                "quinaryZone" => [
                    "start" => 90,
                    "end" => 100
                ]
            ],
            "sleepTime" => [
                "max" => 10,
                "min" => 6
            ]    
        ];
        $this->HRzones = generateHRzones();
        $this->timeInHrZones = generateTimeInHrZones();
    }

    public function getData(): array
    {
        $arr = ["heartRate"=>0,
            "maxHR"=> 0,
            "variableHR"=> 0,
            "RestHR"=> 0,
            "HRzones"=> 0,
            "sleepTime"=> 0,
            "RPEsurvey"=> 0,
            "MentalHealthSurvey"=> 0];

        $arr["heartRate"] = $this->generateRandomInRange($this->ranges["heartRate"], true);

        $arr["maxHR"] = $this->ranges["heartRate"]["max"];

        $arr["RestHR"] = $this->generateRandomInRange($this->ranges["RestHR"], true);

        $arr["variableHR"] = $this->generateRandomInRange($this->ranges["HRV"]);
        #this code would be for genuinely mocking the time spent in each zone but is deprecated;
        // $this->HRzones = $this->generateHRzones();
        // $timeInQuinaryZone = $this->generateRandomInRange(["max"=>$this->time,"min"=>1]);
        // $timeInOtherZone = $this->time - $timeInQuinaryZone;
        $arr["HRzones"] = $this->time;

        $arr["sleepTime"] = $this->generateRandomInRange($this->ranges["sleepTime"]);
        
        return $arr;
    }
    
    private function generateTimeInHrZones(): object;
    {
		
	}

    private function calculateHRZones(): float
    {
        return 0.1;
    }

    private function generateRandomInRange(array $range, bool $intExpected = false): mixed
    {
        $max = $range["max"];
        $min = $range["min"];
        $result = ($min + lcg_value() * (abs($max - $min)));
        $result = number_format($result, 2);
        $result = floatval($result);
        if($intExpected) {
            $result = intval($result);
        }
        return $result;
    }

    private function generateHRzones(): array
    {
        $maxHR = $this->ranges["heartRate"]["max"];
        $HRzonesPercentages = $this->ranges["HRzones"];
        $arr = ["primaryZone" => [
            "start" => $this->getPercentage($maxHR, $HRzonesPercentages["primaryZone"]["start"]),
            "end" => $this->getPercentage($maxHR, $HRzonesPercentages["primaryZone"]["end"])
        ],
        "secondaryZone" => [
            "start" => $this->getPercentage($maxHR, $HRzonesPercentages["secondaryZone"]["start"]),
            "end" => $this->getPercentage($maxHR, $HRzonesPercentages["secondaryZone"]["end"])
        ],
        "tertiaryZone" => [
            "start" => $this->getPercentage($maxHR, $HRzonesPercentages["tertiaryZone"]["start"]),
            "end" => $this->getPercentage($maxHR, $HRzonesPercentages["tertiaryZone"]["end"])
        ],
        "quaternaryZone" => [
            "start" => $this->getPercentage($maxHR, $HRzonesPercentages["quaternaryZone"]["start"]),
            "end" => $this->getPercentage($maxHR, $HRzonesPercentages["quaternaryZone"]["end"])
        ],
        "quinaryZone" => [
            "start" => $this->getPercentage($maxHR, $HRzonesPercentages["quinaryZone"]["start"]),
            "end" => $this->getPercentage($maxHR, $HRzonesPercentages["quinaryZone"]["end"])
        ]];
        return $arr;
    }

    private function getPercentage(mixed $number, int $percent): mixed
    {
        $percentInDecimal = $percent / 100;
        $percentageOfNumber = $percentInDecimal * $number;
        return $percentageOfNumber;
    }
}
