<?php
//
// ���o�B�Ǹ��, �̫���Ʀs�b data �}�C��
//
include_once "../../include/sfs_case_dataarray.php";
$sql_select = "select * from school_room  where enable=1 order by room_id";

$res=$CONN->Execute($sql_select);
$row=$res->getRows();

$data=array();

foreach ($row as $V) {
    $data[$V['room_id']]['room_id']=$V['room_id'];
    $data[$V['room_id']]['room_name']=$V['room_name'];
}