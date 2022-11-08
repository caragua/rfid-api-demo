<?php

    class DB 
    {   
        protected $dbConn = null;

        //建構子, 宣告實體化後會自動連接資料庫
        public function __construct($name, $server, $user, $pwd)
        //function connect_db()
        {
            try {           
                $this->dbConn = new PDO("mysql:dbname=$name;host=$server;charset=utf8mb4", $user, $pwd);
                //開啟長連接
                //$this->dbConn->prepare('SET NAMES "utf8"'); 
            }
            catch(PDOException $e){
                //error message
                trigger_error($e->getMessage()); 
            }
        }

        public function read()
        {
            try{
                // 抓取 var 的總量
                $n = func_num_args();
                // 第一個 var 為 query 的原形，接下來的所有參數都是要加入的值
                $q = func_get_arg(0);
                $args = [];
                for($i = 1; $i < $n; $i++){
                    $args[] = trim(func_get_arg($i));
                }
                // 預備 query 的內容
                $cmd = $this->dbConn->prepare($q);
                // 加入預備好的值並執行 query
                $res = $cmd->execute($args);
                if($res === false){
                    trigger_error(json_encode($cmd->errorInfo()));
                    return $res;
                }
                return $cmd->fetchAll(PDO::FETCH_ASSOC);
            }
            catch(PDOException $e){
                trigger_error($e->getMessage());             
            }  
            catch(Exception $e){
                trigger_error($e->getMessage());             
            }   
        }
        
        public function write()
        {
            try{
                // 抓取 var 的總量
                $n = func_num_args();
                // 第一個 var 為 query 的原形，接下來的所有參數都是要加入的值
                $q = func_get_arg(0);
                $args = [];
                for($i = 1; $i < $n; $i++){
                    $args[] = trim(func_get_arg($i));
                }
                // 預備 query 的內容
                $cmd = $this->dbConn->prepare($q);
                // 加入預備好的值並執行 query
                $res = $cmd->execute($args);
                if($res === false){
                    trigger_error(json_encode($cmd->errorInfo()));
                    return $res;
                }
                return $this->dbConn->lastInsertId();
            }
            catch(PDOException $e){
                trigger_error($e->getMessage());
            }  
            catch(Exception $e){
                trigger_error($e->getMessage());
            }   
        }

        //解構子, 程式執行完畢後會自動斷開資料庫連接
        public function __destruct() 
        {
            $this->dbConn = null;
        } 
        
    }