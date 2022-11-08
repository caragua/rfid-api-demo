<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/sites.php");

    $sites = new Sites($db);

    $fieldList = explode(' ', 'name location');

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

    $result = $sites->add(
        $userInput['name'],
        $userInput['location'],
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    