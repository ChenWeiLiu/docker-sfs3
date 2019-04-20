<?php
// $Id: nor_del.php 5310 2009-01-10 07:57:56Z hami $
/*引入學務系統設定檔*/
include "config.php";

//使用者認證
sfs_check();

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

//取得正確任教課程
$course_arr_all=get_teacher_course(curr_year(),curr_seme(),$_SESSION[session_tea_sn],$is_allow);
$course_arr = $course_arr_all['course'];
// 檢查課程權限是否正確
$cc_arr=array_keys($course_arr);
$err=(in_array($_GET[teacher_course],$cc_arr))?0:1;

if ($err==0) {
	$nor_score="nor_score_".curr_year()."_".curr_seme();
	$sql2  ="DELETE from $nor_score WHERE freq='{$_GET['del']}' and class_subj='{$_GET['class_subj']}' and stage='{$_GET['stage']}'";
	//echo $sql2;
	$CONN->Execute($sql2);
}
header("Location:normal.php?teacher_course={$_GET['teacher_course']}&curr_sort={$_GET['stage']}");
?>
