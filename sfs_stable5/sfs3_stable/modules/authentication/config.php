<?php
//$Id: config.php 6064 2010-08-31 12:26:33Z infodaes $
//預設的引入檔，不可移除。
include_once "../../include/config.php";
require_once "./module-cfg.php";
include_once "../../include/sfs_case_dataarray.php";

//您可以自己加入引入檔

//取得模組參數的類別設定
$m_arr = get_module_setup("authentication");

//取得教師所處處室
$my_sn=$_SESSION['session_tea_sn'];
$my_name=$_SESSION['session_tea_name'];
$sql="select post_office,teach_title_id from teacher_post where teacher_sn=$my_sn;";
$rs=$CONN->Execute($sql) or die("無法取得您的所在處室!<br>$sql");
$my_room_id=$rs->fields['post_office'];
$my_title=$title_kind[($rs->fields['teach_title_id'])];

//學期別
$curr_year_seme=sprintf("%03d%d",curr_year(),curr_seme());
if($work_year_seme=='') $work_year_seme = $curr_year_seme;

//取得處室陣列
$room_kind_array=room_kind();

//取得教師陣列
$teacher_array=teacher_array();

// 取出班級名稱陣列
$class_base=class_base($work_year_seme);

?>
