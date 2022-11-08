<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/cardReaders.php");

    if(!isset($_GET['id']))
    {
        $utils->result(400, [
            "error" => "Missing field: id"
        ]);
    }

    $userInput = json_decode($userInput, true);

    $cardReaders = new CardReaders($db);

    $fieldList = explode(' ', 'serial nickname systemName purpose data status');

    foreach($fieldList as $field)
    {
        if (!isset($userInput[$field]))
        {
            $utils->result(400, [
                "error" => "Missing field: $field"
            ]);
        }
    }

    $result = $cardReaders->update(
        $_GET['id'],
        $userInput['serial'],
        $userInput['nickname'],
        $userInput['systemName'],
        $userInput['purpose'],
        $userInput['data'],
        $userInput['status']
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    