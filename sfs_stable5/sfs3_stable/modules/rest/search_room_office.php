<?php
//
// 取得處室資料, 最後把資料存在 data 陣列中
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