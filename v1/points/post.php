<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/points.php");

    $points = new Points($db);

    $fieldList = explode(' ', 'attendeeId quantity description');

    $userInput = json_decode($userInput, true);

    foreach($fieldList as $field)
    {
        if (!isset($userInput[$field]))
        {
            $utils->result(400, [
                "error" => "Missing field: $field"
            ]);
        }
    }

    $result = $points->add(
        $userInput['attendeeId'],
        $userInput['quantity'],
        $userInput['description']
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    