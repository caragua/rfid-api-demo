<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/sites.php");

    $sites = new Sites($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $sites->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Site not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "site"  => $result,
            "codes" => $sites->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $sites->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $sites->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "sites" => $result,
            "codes" => $sites->codes
        ];

        $utils->result(200, $output, 'json');
    }