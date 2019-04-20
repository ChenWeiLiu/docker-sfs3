<?php
//
// 取得教師認證資訊功能
// 依據教師的身分證字號 , 回傳  teacher_base 中的 login_pass 值,
//  login_pass 為編碼過的雜湊值，第三方應用程式需自行將表單的密碼編碼後比對
//
//  $params['teach_person_id'] 身分證字號 傳入的身分證字號已進行處理, 不以明碼傳送
//
//
    //只取最新一筆 , 依 move_date 排序
    $sql_select = "select login_pass from teacher_base  where sha2(teach_person_id, 256) = '".$params['teach_person_id']."' and teach_person_id!=''";
    $recordSet=$CONN->Execute($sql_select) or die($sql_select);

    if ($recordSet->RecordCount()>0) {
        $row=$recordSet->fetchRow();
        $SERVICE['result']=1;
        $data['login_pass']=$row['login_pass'];             //注意, 返回值必須是 array
    } else {
        //$data=array();
        $SERVICE['result']=-1;
        $SERVICE['message']="查無此人!";
        $data="";
    }
