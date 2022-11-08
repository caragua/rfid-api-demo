<?php

    /*
        +--------------+--------------+------+-----+---------------------+-------------------------------+
        | Field        | Type         | Null | Key | Default             | Extra                         |
        +--------------+--------------+------+-----+---------------------+-------------------------------+
        | id           | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | cardReaderId | int(11)      | YES  |     | NULL                |                               |
        | cardUID      | varchar(16)  | YES  |     | NULL                |                               |
        | description  | varchar(128) | YES  |     | NULL                |                               |
        | status       | tinyint(4)   | YES  |     | 1                   |                               |
        | created      | datetime     | YES  |     | current_timestamp() |                               |
        | updated      | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +--------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class Scans
    {
        protected $db;

        public $codes = [
            'status' =>
            [
                0 => '待處理',
                1 => '處理中',
                2 => '已處理',
                8 => '無效',
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
                        cardReaderId,
                        cardUID,
                        description,
                        status,
                        created,
                        updated
                    from
                        scans
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        updated desc
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function getByCardReaderId($cardReaderId)
        {
            return $this->get("cardReaderId = " . intval($cardReaderId));
        }

        public function add($cardReaderId, $cardUID, $description, $status = 0)
        {
            $fields = explode(' ', 'status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            $result = $this->db->write(
                "
                    insert into scans
                    (   cardReaderId, cardUID, description, status  ) values
                    (   ?,            ?,       ?,           ?       )
                ",
                intval($cardReaderId),
                $cardUID,
                $description,
                intval($status)
            );
            
            return $result;
        }

        public function update($id, $cardReaderId, $cardUID, $description, $status)
        {
            $fields = explode(' ', 'status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            
            $result = $this->db->write(
                "
                    update scans set
                        cardReaderId    = ?,
                        cardUID         = ?,
                        description     = ?,
                        status          = ?
                    where id    = ?
                ",
                intval($cardReaderId),
                $cardUID,
                $description,
                intval($status),
                intval($id)
            );

            return $result;
        }

        // 測試，好像只有 scan 這邊有這種需求
        public function quickUpdate($id, $colName, $value)
        {
            if (in_array($colName, arrya_keys($this->codes)))
            {
                if (!in_array($value, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }

            if(is_int($value))
            {
                $value = intval($value);
            }

            $result = $this->db->write("update scans set $colName = ? where id = ?", $value, intval($id));

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update scans set status = 9 where id = ?", intval($id));
        }
    }