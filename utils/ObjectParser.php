<?php declare(strict_types=1);

namespace utils;

final class ObjectParser
{
    private object $dataObject;

    function __construct()
    {
        #make path universal!
        define("FILE_PATH", "./storage/data.json");
        $this->dataObject = $this->initiateDataBase();
    }

    private function initiateDataBase(): mixed
    {
		if(file_exists(FILE_PATH)) {
            if(filesize(FILE_PATH) != 0) {
                $data = file_get_contents(FILE_PATH);
                return json_decode($data);
            } else {
                return $this->createDataFile();
            }
        } else {
            return $this->createDataFile();
        }
    }

    private function createDataFile(): object
    {
        $file = fopen(FILE_PATH, "a+");
        $data = file_get_contents("./setup/sample.json");
        file_put_contents(FILE_PATH, $data);
        return json_decode($data);
    }

    public function getData(): object
    {
        return $this->dataObject;
    }

    public function updateData(string $key, mixed $value): bool
    {
        $data = $this->dataObject;
        $dataArray = json_decode(json_encode($data), true);
        $dataArray[$key] = $value;
        $data = json_encode($dataArray);
        file_put_contents(FILE_PATH, $data);
        if($this->checkUpdateSuccess($key, $value)) {
            $this->dataObject = json_decode($data);
            return true;
        } else {
            return false;
        }
    }

    public function updateDataWithObject(object $value): void
    {
        $dataObject = $this->dataObject;
        $data = $dataObject->objects;
        $dataArray = json_decode(json_encode($data), true);
        $dataArray[$value->ID] = json_decode(json_encode($value), true);
        $dataObject->objects = json_decode(json_encode($dataArray));
        file_put_contents(FILE_PATH, json_encode($dataObject));
        $this->dataObject = $dataObject;  
    }
	// IMPLEMENT CHECKUPDATESUCCESS FUNCTIONALITY FOR `$this->updateDataWithObject((object) $value);`
	private function checkUpdateSuccess(mixed $key, mixed $value): bool
    {
        $data = file_get_contents(FILE_PATH);
        $dataArray = json_decode($data, true);
        if($dataArray[$key] == $value) { 
            return true; 
        } else if($dataArray[$key] == $this->dataObject->{$key}) { 
            return true; 
        } else { 
            return false; 
        }
    }
    
    public function checkLastFinalFatigueID(): int
    {
        $data = file_get_contents(FILE_PATH);
        $dataArray = json_decode($data, true);
        $lastFinalFatigueID = array_key_last($dataArray["objects"]);
        return intval($lastFinalFatigueID);
    }
}
