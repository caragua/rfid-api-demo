<?php

    $rootDir = "../..";

    require_once("$rootDir/conf/config.php");

    require_once("$rootDir/common/db.php");
    require_once("$rootDir/common/utils.php");

    require_once("$rootDir/common/shared.php");

    require_once("$rootDir/class/scans.php");
    require_once("$rootDir/class/cardReaders.php");
    require_once("$rootDir/class/attendees.php");
    require_once("$rootDir/class/accessRules.php");

    $scans = new Scans($db);
    $cardReaders = new CardReaders($db);
    $attendees = new Attendees($db);
    $accessRules = new AccessRules($db);

    // 讀卡機上報的 自身 mac address
    $fieldList = explode(' ', 'serial cardUID description');

    $userInput = json_decode($userInput, true);

    foreach($fieldList as $field)
    {
        if (!isset($userInput[$field]))
        {
            $utils->result(400, [
                "error" => "Missing field: $field"
            ]);
        }
    }

    // echo "$userInput[serial]";

    // 將讀卡機上報的 mac address 轉換成 cardReaderId
    $reader = $cardReaders->getBySerial($userInput['serial']);

    if ($reader === false || $reader === NULL) {
        $utils->result(404, [
            "error" => "Card reader not found"
        ]);
    }

    $reader = $reader[0];

    // 除了櫃臺使用以外都要額外做後續處理
    // 入場 -> 確認入場資格
    // 加扣分 -> 加扣分

    switch($reader['purpose']) 
    {
        // 櫃臺讀卡機，不處理東西，單純記錄
        case 0:
            $result = $scans->add(
                $reader['id'],
                $userInput['cardUID'],
                $userInput['description']
            );

            if($result === false || $result === NULL)
            {
                $utils->dbError();
            }

            break;

        // 入場管理
        case 1:
            // 取得參加者資訊
            $attendee = $attendees->getByCardUID($userInput['cardUID']);
            
            if ($attendee === false || $attendee === NULL) {
                $utils->result(404, [
                    "error" => "Attendee not found"
                ]);
            }
        
            $attendee = $attendee[0];

            // 取得入場管理的設定
            $rule = $accessRules->getById(intval($reader['data']));
            
            if ($rule === false || $rule === NULL) {
                $utils->result(404, [
                    "error" => "Access rule not found"
                ]);
            }

            $rule = $rule[0];

            if ($attendee['attendeeType'] < $rule['attendeeTypeCheck'])
            {
                $result = $scans->add(
                    $reader['id'],
                    $userInput['cardUID'],
                    "$attendee[serial] 入場失敗（身份） $rule[description]",
                    2
                );

                $utils->plainResult(200, "BAD,Fail: Type");
            }

            if ($rule['ageCheck'] === 1 && $attendee['isMinor'] === 1)
            {
                $result = $scans->add(
                    $reader['id'],
                    $userInput['cardUID'],
                    "$attendee[serial] 入場失敗（年齡） $rule[description]",
                    2
                );

                $utils->plainResult(200, "BAD,Fail: Age");
            }

            // single check??
            // ??
            
            $result = $scans->add(
                $reader['id'],
                $userInput['cardUID'],
                "$attendee[serial] 入場成功 $rule[description]",
                2
            );

            if($result === false || $result === NULL)
            {
                $utils->dbError();
            }

            $rickRoll = "440,16, 494,16, 587,16, 494,16, 740,-8, 740,-8, 659,-4, 440,16, 494,16, 587,16, 494,16, 659,-8, 659,-8, 587,-8, 554,16, 494,-8, 440,16, 494,16, 587,16, 494,16, 587,4, 659,8, 554,-8, 494,16, 440,8, 440,8, 440,8, 659,4, 587,2, 440,16, 494,16, 587,16, 494,16, 740,-8, 740,-8, 659,-4, 440,16, 494,16, 587,16, 494,16, 880,4, 554,8, 587,-8, 554,16, 494,8, 440,16, 494,16, 587,16, 494,16, 587,4, 659,8, 554,-8, 494,16, 440,4, 440,8, 659,4, 587,2, 0,4";
            // $rickRoll = "440,16, 494,16, 587,16, 494,16, 740,-8, 740,-8, 659,-4, 440,16, 494,16, 587,16, 494,16, 659,-8, 659,-8, 587,-8, 554,16, 494,-8, 440,16, 494,16, 587,16, 494,16, 587,4, 659,8, 554,-8, 494,16, 440,8, 440,8, 440,8, 659,4, 587,2";
            // $rickRoll = "440,16, 494,16, 587,16, 494,16, 740,-8, 740,-8, 659,-4, 440,16, 494,16, 587,16, 494,16, 659,-8, 659,-8, 587,-8, 554,16, 494,-8";
            $utils->plainResult(200, "OK,$attendee[serial]");
            // $utils->plainResult(200, "ROLL,$rickRoll");

            break;
        // 加扣點數
        case 2:
    }

    