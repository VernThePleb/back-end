<?php declare(strict_types=1);

namespace utils;
use utils\ObjectParser;
use DateTime;
use utils\Sensors;
require_once("Sensors.php");
require_once("ObjectParser.php");

final class DataMuncher
{
    private float $finalFatigue;

    private object $composition;

    private object $normalisation;

    private ObjectParser $parser;

    function __construct(array $data)
    {
        $this->parser = new ObjectParser();

        $this->composition = $this->generateCompositionFromData($data);

        $this->normalisation = json_decode('{
            "heartRate": 1.5,
            "maxHR": 1,
            "variableHR": -2,
            "RestHR": 2.5,
            "HRzones": 5,
            "sleepTime": -3,
            "RPEsurvey": 3,
            "MentalHealthSurvey": -1
        }');

        $this->finalFatigue = $this->generateFinalFatigue();
    }

    private function generateFinalFatigue(): float
    {
        $normalisedData = $this->normaliseComposition();

        $sumOfNormalisedData = $this->sum($normalisedData);

        $meanOfNormalisedData = $this->mean($sumOfNormalisedData, count($normalisedData));

        $differencialOfNormalisedData = $this->differentiateData($meanOfNormalisedData, $normalisedData);

        $poweredTwoNormalisedData = $this->raiseDataToPowerTwo($differencialOfNormalisedData);

        $sumOfPoweredNormalisedData = $this->sum($poweredTwoNormalisedData);

        $specialMeanOfPoweredNormalisedData = $this->mean($sumOfPoweredNormalisedData, count($poweredTwoNormalisedData), true);

        $squareRootOfSpecialMeanNormalisedData = sqrt($specialMeanOfPoweredNormalisedData);

        $standardDeviatedData = [$squareRootOfSpecialMeanNormalisedData,  $meanOfNormalisedData];

        $sumOfStandardDeviatedData = $this->sum($standardDeviatedData);

        $meanOfStandardDeviatedData = $this->mean($sumOfStandardDeviatedData, count($standardDeviatedData));

        $differencialOfMeanAndStandardDeviation = $this->differentiateData($meanOfStandardDeviatedData, $standardDeviatedData);

        $poweredTwoStandardDeviatedData = $this->raiseDataToPowerTwo($differencialOfMeanAndStandardDeviation);
        
        $sumOfPoweredStandardDeviatedData = $this->sum($poweredTwoStandardDeviatedData);

        $specialMeanOfPoweredStandardDeviatedData = $this->mean($sumOfPoweredStandardDeviatedData, count($standardDeviatedData), true);

        $squareRootOfSpecialMeanStandardDeviatedData = sqrt($specialMeanOfPoweredStandardDeviatedData);

        $this->finalFatigue =  $squareRootOfSpecialMeanStandardDeviatedData;

        return $this->finalFatigue;
    }

    private function mean(float $sum, float $length, bool $special = false): float
    {
        if ($special) {
            $length = $length - 1;
        }
        return $sum / $length;
    }

    private function sum(array $data): float {
        $sum = 0;
        foreach ($data as $datum => $value) {
            $sum = $sum + $value;
        }
        return $sum;
    }

    private function raiseDataToPowerTwo(array $data): array
    {
        $power = 2;
        $result = [];
        foreach ($data as $datum => $value) {
            $result[$datum] = pow($value, $power);
        }
        return $result;
    }

    private function differentiateData(float $mean, array $data): array 
    {
        $result = [];
        foreach ($data as $datum => $value) {
            $result[$datum] = number_format(($value  - $mean), 5);
        }
        return $result;
    }
    
    private function normaliseComposition(): array
    {
        $data = $this->composition;
        $iretableData = json_decode(json_encode((array) $data), true); 
        $result = [];
        foreach ($iretableData as $datum => $value) {
            $result[$datum] = number_format(($value * $this->normalisation->$datum), 2);
        }        
        return $result;
    }

    private function generateCompositionFromData(array $data): object
    {
        $iretableData = json_decode('{
            "heartRate": 0,
            "maxHR": 0,
            "variableHR": 0,
            "RestHR": 0,
            "HRzones": 0,
            "sleepTime": 0,
            "RPEsurvey": 0,
            "MentalHealthSurvey": 0
        }', true);
        foreach ($data as $datum => $value) {
            $iretableData[$datum] = $value;
        }
        return json_decode(json_encode($iretableData));
    } 

    public function fetchFinalFatigue(): object
    {
        $result = json_decode('{
            "ID": 0,
            "value": 0,
            "date": "2022-10-31T09:00:00.594Z",
            "composition": {
                "heartRate": 0,
                "maxHR": 0,
                "variableHR": 0,
                "RestHR": 0,
                "HRzones": 0,
                "sleepTime": 0,
                "RPEsurvey": 0,
                "MentalHealthSurvey": 0
            }
        }', true);
        $countFinalFatigues = $this->parser->checkLastFinalFatigueID();
        $result["ID"] = $countFinalFatigues + 1;
        $result["value"] = $this->finalFatigue;
        $result["date"] = $this->getCurrentDateTimeFormatISO8601();
        $result["composition"] = $this->composition;
        return json_decode(json_encode($result));
    }

    private function getCurrentDateTimeFormatISO8601(): string
    {
        $datum = new DateTime();
        return $datum->format('Y-m-d\TH:i:s\Z');
    }
}
// $arr = ["heartRate"=>85,
//     "maxHR"=> 197,
//     "variableHR"=> 29,
//     "RestHR"=> 68,
//     "HRzones"=> 26.75,
//     "sleepTime"=> 6.1,
//     "RPEsurvey"=> 3,
//     "MentalHealthSurvey"=> 3];

