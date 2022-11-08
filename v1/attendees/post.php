<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/attendees.php");

    $attendees = new Attendees($db);

    $fieldList = explode(' ', 'serial attendeeType status nameCardStatus nickname realname phone email personalID isMinor cardUID team');

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

    $result = $attendees->add(
        $userInput['serial'],
        $userInput['attendeeType'],
        $userInput['status'],
        $userInput['nameCardStatus'],
        $userInput['nickname'],
        $userInput['realname'],
        $userInput['phone'],
        $userInput['email'],
        $userInput['personalID'],
        $userInput['isMinor'],
        $userInput['cardUID'],
        $userInput['team']
    );

    if($result === false || $result === NULL)
    {
        $utils->dbError();
    }

    $utils->result();

    