<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/attendees.php");

    $attendees = new Attendees($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $attendees->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Attendee not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "attendee"  => $result,
            "codes"     => $attendees->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $attendees->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $attendees->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "attendees" => $result,
            "codes"     => $attendees->codes
        ];

        $utils->result(200, $output, 'json');
    }