<?php
//
// 查詢是否有此轉出學生,最後把資料存在 data 陣列中
//
//  $params['person_id'] 身分證字號
//
    //只取最新一筆 , 依 move_date 排序
    $sql_select = "select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
    $recordSet=$CONN->Execute($sql_select) or die($sql_select);

    if ($recordSet->RecordCount()>0) {
        $row=$recordSet->fetchRow();
        //是否過期
        if (strtotime(date("Y-m-d"))>strtotime($row['download_deadline']." 23:59:59")) {
            $data=array();
            $SERVICE['result']=-1;
            $SERVICE['message']="下載期限已過!";
        } elseif ($row['download_times']>=$row['download_limit']) {
            $data = array();
            $SERVICE['result'] = -1;
            $SERVICE['message'] = "已超過下載次數限制! (".$row['download_limit']."次)";
        } else {
            $data=$row;
        }
    } else {
        //$data=array();
        $SERVICE['result']=-1;
        $SERVICE['message']="查無此學生轉出記錄!";
        $data="select a.*,b.stud_name,b.stud_person_id from stud_move a,stud_base b where a.school_id='".$params['request_edu_id']."' and a.student_sn=b.student_sn and a.move_kind=8 and b.stud_person_id='".$params['stud_person_id']."' order by move_date desc limit 1";
    }
