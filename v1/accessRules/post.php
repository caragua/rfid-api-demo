<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/accessRules.php");

    $accessRules = new AccessRules($db);

    $fieldList = explode(' ', 'siteId description attendeeTypeCheck ageCheck singlePass');

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

    $result = $accessRules->add(
        $userInput['siteId'],
        $userInput['description'],
        $userInput['attendeeTypeCheck'],
        $userInput['ageCheck'],
        $userInput['singlePass']
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    