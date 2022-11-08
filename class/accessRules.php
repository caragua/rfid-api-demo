<?php

    /*
        +-------------------+--------------+------+-----+---------------------+-------------------------------+
        | Field             | Type         | Null | Key | Default             | Extra                         |
        +-------------------+--------------+------+-----+---------------------+-------------------------------+
        | id                | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | siteId            | int(11)      | YES  |     | NULL                |                               |
        | description       | varchar(128) | YES  |     | NULL                |                               |
        | attendeeTypeCheck | tinyint(4)   | YES  |     | NULL                |                               |
        | ageCheck          | tinyint(1)   | YES  |     | NULL                |                               |
        | singlePass        | tinyint(1)   | YES  |     | NULL                |                               |
        | status            | tinyint(4)   | YES  |     | 1                   |                               |
        | created           | datetime     | YES  |     | current_timestamp() |                               |
        | updated           | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +-------------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class AccessRules
    {
        protected $db;

        public $codes = [
            'attendeeTypeCheck' => 
            [
                0  => '無限制',
                10 => '參加者',
                11 => '參加者（一日）',
                12 => '參加者（二日）',
                20 => '贊助者',
                30 => '超級贊助者'
            ],
            'ageCheck' => 
            [
                0 => '無限制',
                1 => '限成年者入場'
            ],
            'singlePass' =>
            [
                0 => '無限制',
                1 => '限單次入場'
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
                        siteId,
                        description,
                        attendeeTypeCheck,
                        ageCheck,
                        singlePass,
                        status,
                        created,
                        updated
                    from
                        accessRules
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        siteId
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function add($siteId, $description, $attendeeTypeCheck, $ageCheck, $singlePass)
        {
            $fields = explode(' ', 'attendeeTypeCheck ageCheck singlePass');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }

            $result = $this->db->write(
                "
                    insert into accessRules
                    (   siteId, description, attendeeTypeCheck, ageCheck, singlePass    ) values
                    (   ?,      ?,           ?,                 ?,        ?             )
                ",
                intval($siteId), 
                $description, 
                intval($attendeeTypeCheck), 
                intval($ageCheck), 
                intval($singlePass),
            );

            return $result;
        }

        public function update($id, $siteId, $description, $attendeeTypeCheck, $ageCheck, $singlePass, $status)
        {
            $fields = explode(' ', 'attendeeTypeCheck ageCheck singlePass status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            
            $result = $this->db->write(
                "
                    update accessRules set
                        siteId              = ?, 
                        description         = ?, 
                        attendeeTypeCheck   = ?, 
                        ageCheck            = ?, 
                        singlePass          = ?,
                        status              = ?
                    where id = ?
                ",
                intval($siteId), 
                $description, 
                intval($attendeeTypeCheck), 
                intval($ageCheck), 
                intval($singlePass),
                intval($status),
                intval($id)
            );

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update accessRules set status = 9 where id = ?", intval($id));
        }
    }