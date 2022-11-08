<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/accessRules.php");

    $userInput = json_decode($userInput, true);

    if(!isset($_GET['id']))
    {
        $utils->result(400, [
            "error" => "Missing field: id"
        ]);
    }

    $accessRules = new AccessRules($db);

    $fieldList = explode(' ', 'siteId description attendeeTypeCheck ageCheck singlePass status');

    foreach($fieldList as $field)
    {
        if (!isset($userInput[$field]))
        {
            $utils->result(400, [
                "error" => "Missing field: $field"
            ]);
        }
    }

    $result = $accessRules->update(
        $_GET['id'],
        $userInput['siteId'],
        $userInput['description'],
        $userInput['attendeeTypeCheck'],
        $userInput['ageCheck'],
        $userInput['singlePass'],
        $userInput['status'],
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    