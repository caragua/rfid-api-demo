<?php

    /*
        +------------+--------------+------+-----+---------------------+-------------------------------+
        | Field      | Type         | Null | Key | Default             | Extra                         |
        +------------+--------------+------+-----+---------------------+-------------------------------+
        | id         | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | serial     | varchar(32)  | YES  |     | NULL                |                               |
        | nickname   | varchar(128) | YES  |     | NULL                |                               |
        | systemName | varchar(32)  | YES  |     | NULL                |                               |
        | purpose    | tinyint(4)   | YES  |     | NULL                |                               |
        | data       | varchar(256) | YES  |     | NULL                |                               |
        | status     | tinyint(4)   | YES  |     | 1                   |                               |
        | created    | datetime     | YES  |     | current_timestamp() |                               |
        | updated    | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class CardReaders
    {
        protected $db;

        public $codes = [
            'purpose' => 
            [
                0 => '櫃臺',
                1 => '入場管理',
                2 => '加扣點數'
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
                        serial,
                        nickname,
                        systemName,
                        purpose,
                        data,
                        status,
                        created,
                        updated
                    from
                        cardReaders
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        systemName
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function getBySerial($serial)
        {
            return $this->get("serial = '$serial'");
        }

        public function add($serial, $nickname, $systemName, $purpose, $data)
        {
            $result = $this->db->write(
                "
                    insert into cardReaders
                    (   serial, nickname, systemName, purpose, data ) values
                    (   ?,      ?,        ?,          ?,       ?    )
                ",
                $serial, 
                $nickname, 
                $systemName, 
                intval($purpose), 
                $data
            );

            return $result;
        }

        public function update($id, $serial, $nickname, $systemName, $purpose, $data, $status)
        {
            $fields = explode(' ', 'purpose status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            
            $result = $this->db->write(
                "
                    update cardReaders set
                        serial      = ?,
                        nickname    = ?,
                        systemName  = ?,
                        purpose     = ?,
                        data        = ?,
                        status      = ?
                    where id = ?
                ",
                $serial, 
                $nickname, 
                $systemName, 
                intval($purpose), 
                $data,
                intval($status),
                intval($id)
            );

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update cardReaders set status = 9 where id = ?", intval($id));
        }
    }