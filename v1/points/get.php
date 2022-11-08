<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/points.php");

    $points = new Points($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $points->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Point log not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "point"  => $result,
            "codes" => $points->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['attendeeId']))
    {
        // get only one data
        $result = $points->getByAttendeeId($_GET['attendeeId']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Points log of attendee not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "points"  => $result,
            "codes"   => $points->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $points->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $points->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "points" => $result,
            "codes" => $points->codes
        ];

        $utils->result(200, $output, 'json');
    }