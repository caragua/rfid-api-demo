<?php

    /*
        +-------------------+--------------+------+-----+---------------------+-------------------------------+
        | Field             | Type         | Null | Key | Default             | Extra                         |
        +----------------+--------------+------+-----+---------------------+-------------------------------+
        | id             | int(11)      | NO   | PRI | NULL                | auto_increment                |
        | serial         | varchar(16)  | YES  |     | NULL                |                               |
        | attendeeType   | tinyint(4)   | YES  |     | NULL                |                               |
        | nameCardStatus | tinyint(4)   | YES  |     | NULL                |                               |
        | nickname       | varchar(128) | YES  |     | NULL                |                               |
        | realname       | varchar(64)  | YES  |     | NULL                |                               |
        | phone          | varchar(32)  | YES  |     | NULL                |                               |
        | email          | varchar(64)  | YES  |     | NULL                |                               |
        | personalID     | varchar(16)  | YES  |     | NULL                |                               |
        | isMinor        | tinyint(4)   | YES  |     | 0                   |                               |
        | cardUID        | varchar(16)  | YES  |     | NULL                |                               |
        | team           | tinyint(4)   | YES  |     | NULL                |                               |
        | status         | tinyint(4)   | YES  |     | 1                   |                               |
        | created        | datetime     | YES  |     | current_timestamp() |                               |
        | updated        | timestamp    | NO   |     | current_timestamp() | on update current_timestamp() |
        +----------------+--------------+------+-----+---------------------+-------------------------------+
    */

    class Attendees
    {
        protected $db;

        public $codes = [
            'attendeeType' => 
            [
                10 => '?????????',
                11 => '?????????????????????',
                12 => '?????????????????????',
                20 => '?????????',
                30 => '???????????????'
            ],
            'nameCardStatus' =>
            [
                0 => '?????????',
                1 => '?????????',
                2 => '????????????',
                3 => '????????????'
            ],
            'isMinor' =>
            [
                0 => '?????????',
                1 => '?????????'
            ],
            'team' =>
            [
                0 => 'A',
                1 => 'B'
            ],
            'status' => 
            [
                0 => '???????????????',
                1 => '?????????',
                2 => '????????????',
                9 => '??????????????????'
            ],
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
                        attendeeType,
                        nameCardStatus,
                        nickname,
                        realname,
                        phone,
                        email,
                        personalID,
                        isMinor,
                        cardUID,
                        team,
                        status,
                        created,
                        updated
                    from
                        attendees
                    where
                        $condition
                        status not in ( 9 )
                    order by
                        serial
                "
            );

            return $result;
        }

        public function getById($id)
        {
            return $this->get("id = " . intval($id));
        }

        public function getByCardUID($cardUID)
        {
            return $this->get("cardUID = '$cardUID'");
        }

        public function add($serial, $attendeeType, $status, $nameCardStatus, $nickname, $realname, $phone, $email, $personalID, $isMinor, $cardUID, $team)
        {
            $fields = explode(' ', 'attendeeType nameCardStatus isMinor status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            $result = $this->db->write(
                "
                    insert into attendees
                    (   serial, attendeeType, status, nameCardStatus, nickname, realname, phone, email, personalID, isMinor, cardUID, team  ) values
                    (   ?,      ?,            ?,      ?,              ?,        ?,        ?,     ?,     ?,          ?,       ?,       ?     )
                ",
                $serial,
                intval($attendeeType),
                intval($status),
                intval($nameCardStatus),
                $nickname,
                $realname,
                $phone,
                $email,
                $personalID,
                intval($isMinor),
                $cardUID,
                intval($team)
            );

            return $result;
        }

        public function update($id, $serial, $attendeeType, $status, $nameCardStatus, $nickname, $realname, $phone, $email, $personalID, $isMinor, $cardUID, $team)
        {
            $fields = explode(' ', 'attendeeType nameCardStatus isMinor status');
            foreach($fields as $field)
            {
                if (!in_array(${$field}, array_keys($this->codes[$field])))
                {
                    return false;
                }
            }
            
            $result = $this->db->write(
                "
                    update attendees set
                        serial              = ?,
                        attendeeType        = ?,
                        status              = ?,
                        nameCardStatus      = ?,
                        nickname            = ?,
                        realname            = ?,
                        phone               = ?,
                        email               = ?,
                        personalID          = ?,
                        isMinor             = ?,
                        cardUID             = ?,
                        team                = ?
                    where id                = ?
                ",
                $serial,
                intval($attendeeType),
                intval($status),
                intval($nameCardStatus),
                $nickname,
                $realname,
                $phone,
                $email,
                $personalID,
                intval($isMinor),
                $cardUID,
                intval($team),
                intval($id)
            );

            return $result;
        }

        public function delete($id)
        {
            $result = $this->db->write("update attendees set status = 9 where id = ?", intval($id));
        }
    }