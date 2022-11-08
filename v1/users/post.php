<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/users.php");

    $users = new Users($db);

    $fieldList = explode(' ', 'accountType nickname account password');

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

    $result = $users->add($userInput['accountType'], $userInput['nickname'], $userInput['account'], $userInput['password']);

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    