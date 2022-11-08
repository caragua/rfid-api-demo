<?php

    class Utils
    {
        function result($statusCode = 200, $message = [], $type = false)
        {
            if ($type)
            {
                switch($type)
                {
                    case 'json':
                        header('Content-Type: application/json');
                        break;
                }
            }

            http_response_code($statusCode);
            echo json_encode($message);
            exit();
        }

        function plainResult($statusCode = 200, $message = "")
        {
            http_response_code($statusCode);

            echo $message;

            exit();
        }

        function dbError()
        {
            $this->result(500, [
                "error" => "There is something wrong with the DB, please contact the developers and ask them to check for: timestamp " . date("Y-m-d h:i:sa")
            ]);
        }

        function randomString($length = 8)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[mt_rand(0, $charactersLength - 1)];
            }
            return $randomString;
        }
    }