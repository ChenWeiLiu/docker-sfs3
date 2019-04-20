<?php
//
// 取得在職教師職務資料
//
$person_id=$params['person_id'];
$sql_select = "select a.teach_id,a.name,a.sex,a.teacher_sn,c.room_name,d.title_name,d.teach_title_id from teacher_base a,teacher_post b,school_room c,teacher_title d where  sha2(a.teach_person_id, 256) = '$person_id' and a.teach_condition='0' and a.teacher_sn=b.teacher_sn and b.post_office=c.room_id and b.teach_title_id=d.teach_title_id order by teach_title_id";


$res=$CONN->Execute($sql_select);
$V=$res->fetchRow();
$data['teacher_sn']=$V['teacher_sn'];
$data['teacher_id']=$V['teach_id'];
$data['teacher_name']=$V['name'];
$data['teacher_sex']=$V['sex'];
$data['room_name']=$V['room_name'];
$data['title_name']=$V['title_name'];
$data['full_title']="(".$V['title_name'].") ".$V['name'];

$sql_select = "select * from school_base";
$recordSet=$CONN->Execute($sql_select);
$array = $recordSet->FetchRow();

$data['school_cname']=$array['sch_cname'];