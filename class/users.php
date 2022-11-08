<?php

    /*
        +-------------+--------------+------+-----+---------------------+-------------------------------+
        | Field       | Type         | Null | Key | Default             | Extra                         |
        +-------------+--------------+------+-----+---------------------+-------------------------------+
        | id          | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | accountType | tinyint(4)   | YES  |     | NULL                |                               |
        | nickname    | varchar(128) | YES  |     | NULL                |                               |
        | account     | varchar(64)  | YES  |     | NULL                |                               |
        | hash        | varchar(128) | YES  |     | NULL                |                               |
        | salt        | varchar(16)  | YES  |     | NULL                |                               |
        | status      | tinyint(4)   | NO   |     | 1                   |                               |
        | created     | datetime     | YES  |     | current_timestamp() |                               |
        | updated     | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +-------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class Users
    {
        protected $db;

        public $codes = [
            'accountType' => 
            [
                0 => '無',
                1 => '一般使用者',
                9 => '管理員'
            ],
            'status' =>
            [
                0 => '停用',
                1 => '正常',
                9 => '封存（隱藏）'
            ]
        ];

        public function __construct($dbConn)
        {
            $this->db = $dbConn;
        }
        
        function generatePassword($password)
        {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $salt = '';
            for ($i = 0; $i < 8; $i++) {
                $salt .= $characters[mt_rand(0, $charactersLength - 1)];
            }

            return [
                "salt" => $salt,
                "hash" => hash("sha256", "$salt-$password")
            ];
        }

        public function get($condition = "")
        {
            if(is_array($condition))
            {
                $condition = "id in (" . implode(", ", $condition) . ") and ";
            }
            elseif($condition != "")
            {
                $condition .= " and ";
            }
            
            $result = $this->db->read(
                "
                    select
                        id,
                        accountType,
                        nickname,
                        account,
                        status,
                        created,
                        updated
                    from
                        users
                    where
                        $condition
                        status != 9
                    order by
                        account
                "
            );

            return $result;
        }

        public function getByAccount($account)
        {
            return $this->get("account = '$account'");
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function add($accountType, $nickname, $account, $password)
        {
            $pass = $this->generatePassword($password);
            
            $result = $this->db->write(
                "
                    insert into users
                    (   accountType,    nickname,   account,    hash,   salt    ) values
                    (   ?,              ?,          ?,          ?,      ?       )
                ",
                intval($accountType),
                $nickname,
                $account,
                $pass['hash'],
                $pass['salt']
            );

            return $result;
        }

        public function login($account, $password)
        {
            $user = $this->db->read(
                "
                    select
                        account,
                        salt,
                        hash
                    from
                        users
                    where
                        account = ? and
                        status in ( 1 )
                ",
                $account
            );

            if($user === false || count($user) === 0){
                return $user;
            }

            $user = $user[0];

            if(hash("sha256", "$user[salt]-$password") !== $user['hash'])
            {
                return [];
            }

            return $this->getByAccount($account);
        }

        public function update()
        {

        }

        public function delete($id)
        {
            $result = $this->db->write("update users set status = 9 where id = ?", intval($id));
        }
    }