<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/scans.php");

    $userInput = json_decode($userInput, true);

    if(!isset($_GET['id']))
    {
        $utils->result(400, [
            "error" => "Missing field: id"
        ]);
    }

    $scans = new Scans($db);

    $fieldList = explode(' ', 'cardReaderId cardUID description status');

    foreach($fieldList as $field)
    {
        if (!isset($userInput[$field]))
        {
            $utils->result(400, [
                "error" => "Missing field: $field"
            ]);
        }
    }

    $result = $scans->update(
        $_GET['id'],
        $userInput['cardReaderId'],
        $userInput['cardUID'],
        $userInput['description'],
        $userInput['status']
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    