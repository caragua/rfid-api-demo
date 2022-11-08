<?php

    /*
        +-------------+--------------+------+-----+---------------------+-------------------------------+
        | Field       | Type         | Null | Key | Default             | Extra                         |
        +-------------+--------------+------+-----+---------------------+-------------------------------+
        | id          | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | attendeeId  | int(11)      | YES  |     | NULL                |                               |
        | quantity    | int(11)      | YES  |     | NULL                |                               |
        | description | varchar(256) | YES  |     | NULL                |                               |
        | status      | tinyint(4)   | YES  |     | 1                   |                               |
        | created     | datetime     | YES  |     | current_timestamp() |                               |
        | updated     | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +-------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class Points
    {
        protected $db;

        public $codes = [
            'status' =>
            [
                0 => '無效',
                1 => '正常',
                2 => '待確認',
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
                        attendeeId,
                        quantity,
                        description,
                        status,
                        created,
                        updated
                    from
                        points
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        attendeeId
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function getByAttendeeId($attendeeId)
        {
            return $this->get("attendeeId = " . intval($attendeeId));
        }

        public function add($attendeeId, $quantity, $description)
        {
            $result = $this->db->write(
                "
                    insert into points
                    (   attendeeId, quantity, description   ) values
                    (   ?,          ?,        ?             )
                ",
                intval($attendeeId), 
                intval($quantity), 
                intval($description)
            );

            return $result;
        }

        public function update($id, $attendeeId, $quantity, $description, $status)
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
                    update points set
                        attendeeId  = ?,
                        quantity    = ?,
                        description = ?,
                        status      = ?
                    where id = ?
                ",
                intval($attendeeId), 
                intval($quantity), 
                intval($description),
                intval($status),
                intval($id)
            );

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update points set status = 9 where id = ?", intval($id));
        }
    }