<?php declare(strict_types=1);

include "controllers\DataController";

use controllers\DataController;

if(isset($_POST['action'])) {
    $dataController = new DataController();
    switch ($_POST['action']) 
    {
        case "fetch":
            echo $dataController->fetchData();
            break;
        case "writeFinalFatigue":
            $dataController->writeFinalFatigueEntry($_POST["json"]);
            break;
        case "writeColumn":
            $dataController->saveColumnOfData($_POST["key"], $_POST["value"]);
            break;
        case "fetchMockData":
            echo $dataController->fetchMockData();
            break;
    }
}