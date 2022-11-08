<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/cardReaders.php");

    $cardReaders = new CardReaders($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $cardReaders->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "cardReader not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "cardReader"  => $result,
            "codes" => $cardReaders->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $cardReaders->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $cardReaders->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "cardReaders" => $result,
            "codes" => $cardReaders->codes
        ];

        $utils->result(200, $output, 'json');
    }