<?php
//
// 取得班級名單列表 , 最後把資料存在 data 陣列中
//
$params['class_id'];

$c=explode("_",$params['class_id']);

$seme_year_seme=sprintf("%03d%d",$c[0],$c[1]);
$seme_class=sprintf("%d%02d",$c[2],$c[3]);

$sql_select = "select a.student_sn,a.stud_id,a.seme_class,a.seme_num,b.stud_name,b.stud_sex from stud_seme a,stud_base b where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and a.student_sn=b.student_sn and (b.stud_study_cond='0' or b.stud_study_cond='15') ";

$res=$CONN->Execute($sql_select);
$row=$res->getRows();
$i=0;
foreach ($row as $V) {
    $i++;
    $data[$i]['student_sn']=$V['student_sn'];
    $data[$i]['stud_id']=$V['stud_id'];
    $data[$i]['stud_name']=$V['stud_name'];
    $data[$i]['stud_class']=$V['seme_class'];
    $data[$i]['stud_sitenum']=$V['seme_num'];
    $data[$i]['stud_sex']=$V['stud_sex'];
}