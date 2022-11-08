<?php

    //header('Content-Type: application/json');
    // 預設顯示 500 internal error
    http_response_code(500);

    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    session_start();

    foreach($_GET as $key => $value)
    {
        if(!is_array($value))
        {
            $_GET[$key] = trim($value);
        }
    }

    foreach($_POST as $key => $value)
    {
        if(!is_array($value))
        {
            $_POST[$key] = trim($value);
        }
    }

    $userInput = file_get_contents('php://input');

    $utils = new Utils();

    $db = new DB(
        $config["db"]["name"],
        $config["db"]["server"],
        $config["db"]["user"],
        $config["db"]["password"]
    );