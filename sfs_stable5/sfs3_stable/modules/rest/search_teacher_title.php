<?php
//
// ���o¾��, �̫���Ʀs�b data �}�C��
//
include_once "../../include/sfs_case_dataarray.php";
$sql_select = "select a.*,b.room_name from teacher_title a,school_room b where a.enable=1 and a.room_id=b.room_id order by a.rank";


$res=$CONN->Execute($sql_select);
$row=$res->getRows();
$i=0;

//¾�����O
$title_kind_p = post_kind();

foreach ($row as $V) {
    $i++;
    $data[$i]['title_name']=$V['title_name'];
    $data[$i]['title_short_name']=$V['title_short_name'];
    $data[$i]['title_kind']=$title_kind_p[$V['title_kind']];
    $data[$i]['room_name']=$V['room_name'];
}