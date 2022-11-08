<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/accessRules.php");

    $accessRules = new AccessRules($db);

    if(isset($_GET['id']))
    {
        // get only one data
        $result = $accessRules->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "Rule not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "accessRule"  => $result,
            "codes" => $accessRules->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $accessRules->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get list
        $result = $accessRules->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        $output = [
            "accessRules" => $result,
            "codes" => $accessRules->codes
        ];

        $utils->result(200, $output, 'json');
    }