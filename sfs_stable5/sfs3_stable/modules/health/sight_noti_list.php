<?php
/**
 * 列印視力不良清單
 */
require "config.php";

$year = (int) substr($_POST['year_seme'],0,-1);
$semester = (int) substr($_POST['year_seme'], -1);
if ($_POST['class_name'] == 'all')
$class_sql ="";
else
$class_sql = " AND a.curr_class_num LIKE '".(int) $_POST['class_name']."$class%'";

$query = "SELECT a.student_sn, a.stud_name, a.stud_sex, a.curr_class_num, b.* FROM stud_base a, health_sight b
			WHERE a.student_sn=b.student_sn AND b.year=$year AND b.semester=$semester AND a.stud_study_cond=0
			  $class_sql ORDER BY a.curr_class_num ,b.side";
$res = $CONN->Execute($query) or trigger_error($query);

$arr = array();
foreach ($res as $row) {
	$row['grade'] = substr($row['curr_class_num'],0,1);
	$row['class'] = (int)substr($row['curr_class_num'],1,2);
	$row['number'] = (int)substr($row['curr_class_num'],-2);

	$arr[$row['student_sn']][$row['side']] = $row;
}
$manage_item = array(
			"1"=>"視力保建",
			"2"=>"點藥治療",
			"3"=>"配鏡矯治",
			"4"=>"家長未處理",
			"5"=>"更換鏡片",
			"6"=>"定期檢查",
			"7"=>"遮眼治療",
			"8"=>"另類治療",
			"9"=>"配戴隱型眼鏡",
			"N"=>"其它");

$smarty->assign('manage_item', $manage_item);

//print_r(get_school_base());
$smarty->assign("school_data",get_school_base());
$smarty->assign('data', $arr);
$smarty->display('sight_noti_list.tpl');
