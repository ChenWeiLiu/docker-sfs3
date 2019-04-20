<?php
//
// 取得在職教師名單列表 , 最後把資料存在 data 陣列中
//

$limit_teacher_sn=($params['teacher_sn']!='')?"and a.teacher_sn='".$params['teacher_sn']."'":"";

if ($params['room_id']!='') {
    //$sql="select room_id from school_room where room_name='{$params['room']}'";
    //$res=$CONN->Execute($sql_select);
    //$room_id = ($res->recordCount()>0)?$res->fields['room_id']:'0';
    $room_id=$params['room_id'];
    $sql_select = "select a.teach_id,a.name,a.teach_person_id,a.sex,a.teacher_sn,c.room_name,d.rank,d.title_name,d.teach_title_id from teacher_base a,teacher_post b,school_room c,teacher_title d where a.teach_condition='0' and a.teacher_sn=b.teacher_sn and b.post_office='$room_id' and b.post_office=c.room_id and b.teach_title_id=d.teach_title_id $limit_teacher_sn order by d.rank";
} else {
    $sql_select = "select a.teach_id,a.name,a.teach_person_id,a.sex,a.teacher_sn,c.room_name,d.rank,d.title_name,d.teach_title_id from teacher_base a,teacher_post b,school_room c,teacher_title d where a.teach_condition='0' and a.teacher_sn=b.teacher_sn and b.post_office=c.room_id and b.teach_title_id=d.teach_title_id $limit_teacher_sn order by d.rank";
}


$res=$CONN->Execute($sql_select);
$row=$res->getRows();
$i=0;
foreach ($row as $V) {
    $i++;
    $key=($params['key']=='teacher_sn')?$V['teacher_sn']:$i;
    $data[$key]['teacher_sn']=$V['teacher_sn'];
    $data[$key]['teacher_id']=$V['teach_id'];
    $data[$key]['teacher_name']=$V['name'];
    $data[$key]['teacher_sex']=$V['sex'];
    $data[$key]['room_name']=$V['room_name'];
    $data[$key]['title_name']=$V['title_name'];
    $data[$key]['teach_person_id']=$V['teach_person_id'];
    $data[$key]['full_title']="(".$V['title_name'].") ".$V['name'];


    //有帶 POST參數
    if (trim($params['appraisal'])=='need') {
        $sql="select a.*,b.appoint_date,b.arrive_date from `teacher_appraisal` a,teacher_post b where a.teacher_sn='{$V['teacher_sn']}' and a.teacher_sn=b.teacher_sn order by rec_sort desc limit 1";
        $res_appraisal=$CONN->Execute($sql);
        if ($res_appraisal->recordCount()>0) {
            $row_appraisal=$res_appraisal->fetchRow();
            $data[$key]['appraisal_title']=$row_appraisal['title'];
            $data[$key]['years_of_service']=$row_appraisal['years_of_service'];
            $data[$key]['salary_kind']=$row_appraisal['salary_kind'];
            $data[$key]['base_salary']=$row_appraisal['base_salary'];
            $data[$key]['spec_allowance']=$row_appraisal['spec_allowance'];
            $data[$key]['leader_allowance']=$row_appraisal['leader_allowance'];
            $data[$key]['area_kind']=$row_appraisal['area_kind'];
            $data[$key]['area_years_of_service']=$row_appraisal['area_years_of_service'];
            $data[$key]['memo']=$row_appraisal['memo'];
            $data[$key]['appoint_date']=$row_appraisal['appoint_date'];
            $data[$key]['arrive_date']=$row_appraisal['arrive_date'];
        } else {
            $data[$key]['appraisal_title']=0;
            $data[$key]['years_of_service']=0;
            $data[$key]['salary_kind']=0;
            $data[$key]['base_salary']=0;
            $data[$key]['spec_allowance']=0;
            $data[$key]['leader_allowance']=0;
            $data[$key]['area_kind']=0;
            $data[$key]['area_years_of_service']=0;
            $data[$key]['memo']=0;
        }
    }
}