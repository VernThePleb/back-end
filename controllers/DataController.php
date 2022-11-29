<?php declare(strict_types=1);

namespace controllers;
use utils;
use utils\ObjectParser;
use utils\Sensors;
use utils\DataMuncher;
require_once("utils/Sensors.php");
require_once("utils/ObjectParser.php");
require_once("utils/DataMuncher.php");

final class DataController 
{
    public Sensors $sensors;

    private ObjectParser $objectParser;

    function __construct()
    {
        $this->objectParser = new ObjectParser;
        $this->sensors = new Sensors($this->getAge(), 30);
    }

    private function getAge(): int
    {
        $data = $this->objectParser->getData();
        return $data->age;
    }

    public function fetchData(): object
    {
        $data = $this->objectParser->getData();
        return $data;
    }

    public function saveColumnOfData(string $key, mixed $value): bool
    {
        return $this->objectParser->updateData($key, $value);
    }

    public function writeFinalFatigueEntry(object $data): void
    {
        $this->objectParser->updateDataWithObject($data);
    }

    public function fetchMockData(): object
    {
        return $this->sensors->getData();
    }
}

$Controller = new DataController();
$tobesaved = $Controller->sensors->getData();
$dataMuncher = new DataMuncher($tobesaved);
//var_dump($dataMuncher->fetchFinalFatigue());
$Controller->writeFinalFatigueEntry($dataMuncher->fetchFinalFatigue());
