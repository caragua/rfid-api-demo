<?php

    /*
        +----------+--------------+------+-----+---------------------+-------------------------------+
        | Field    | Type         | Null | Key | Default             | Extra                         |
        +----------+--------------+------+-----+---------------------+-------------------------------+
        | id       | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | name     | varchar(128) | YES  |     | NULL                |                               |
        | location | varchar(128) | YES  |     | NULL                |                               |
        | status   | tinyint(4)   | YES  |     | 1                   |                               |
        | created  | datetime     | YES  |     | current_timestamp() |                               |
        | updated  | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +----------+--------------+------+-----+---------------------+-------------------------------+
    */

    class Sites
    {
        protected $db;

        public $codes = [
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
                        name,
                        location,
                        status,
                        created,
                        updated
                    from
                        sites
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        name
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function add($name, $location)
        {
            $result = $this->db->write(
                "
                    insert into sites
                    (   name, location  ) values
                    (   ?,    ?         )
                ",
                $name,
                $location
            );

            return $result;
        }

        public function update($id, $name, $location, $status)
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
                    update sites set
                        name        = ?,
                        location    = ?,
                        status      = ?
                    where id    = ?
                ",
                $name,
                $location,
                intval($status),
                intval($id)
            );

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update sites set status = 9 where id = ?", intval($id));
        }
    }