<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/users.php");

    $users = new Users($db);

    if(isset($_GET['id']))
    {
        // get only one contact
        $result = $users->getById($_GET['id']);

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }

        if(count($result) === 0)
        {
            $utils->result(404, [
                "error" => "User not found"
            ]);
        }

        $result = $result[0];

        $output = [
            "user"  => $result,
            "codes" => $users->codes
        ];

        $utils->result(200, $output, 'json');
    }
    elseif(isset($_GET['codes']))
    {
        $output = [
            "codes" => $users->codes
        ];

        $utils->result(200, $output, 'json');
    }
    else
    {
        // get user list
        $result = $users->get();

        if($result === false || $result === NULL)
        {
            $utils->dbError();
        }
  
        $output = [
            "users" => $result,
            "codes" => $users->codes
        ];

        $utils->result(200, $output, 'json');
    }