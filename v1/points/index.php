<?php    

    $requestType_ = strtolower($_SERVER['REQUEST_METHOD']);

    if($requestType_ != 'option') 
    {
        include("$requestType_.php");
    }

    
    