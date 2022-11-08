<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/scans.php");

    $scans = new Scans($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $scans->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Scan not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "scan"  => $result,
            "codes" => $scans->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['cardReaderId']))
    {
        // get only one data
        $result = $scans->getByCardReaderId($_GET['cardReaderId']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Card reader not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "scans"  => $result,
            "codes" => $scans->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $scans->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $scans->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "scans" => $result,
            "codes" => $scans->codes
        ];

        $utils->result(200, $output, 'json');
    }