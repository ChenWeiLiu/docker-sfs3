<?php

//預設的引入檔，不可移除。
include_once "../../include/config.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";

//學校代碼
$school_id=$SCHOOL_BASE['sch_id'];

$room_kind=room_kind();
$title_kind=title_kind();
$class_name_arr = class_base() ;

//取得教師所處處室
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];
$sql="select post_office,teach_title_id from teacher_post where teacher_sn=$my_sn;";
$rs=$CONN->Execute($sql) or die("無法取得您的所在處室!<br>$sql");
$my_room=$room_kind[($rs->fields['post_office'])];
$my_title=$title_kind[($rs->fields['teach_title_id'])];

$page_break ="<P style='page-break-after:always'>&nbsp;</P>";
?>
