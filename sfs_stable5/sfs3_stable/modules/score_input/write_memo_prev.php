<?php
//header('Content-type: text/html;charset=big5');
include "../../include/config.php";
require "../../include/json.php";
sfs_check();
// 建立 json 物件
$json = new Services_JSON();

$course_id =  $_GET['course_id'];

//$course_id =  $_POST['course_id'];

if ($course_id) {
	$ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;

	// 分組班
	if(strstr ($course_id, 'g')){
		$group_arr=explode("g",$course_id);
		$seme_year_seme = sprintf("%03d%d",$sel_year,$sel_seme);
		$ss_id=$group_arr[1];
		$studentSql="select student_sn from elective_stu where group_id='{$group_arr[0]}' ";

	}else{
		// 先查詢本學期課程
		$query = "SELECT ss_id,class_year,class_name FROM score_course WHERE course_id=$course_id";

		$row = $CONN->Execute($query)->fetchRow() or die($query);
		// 目前的班級
		$class_sn = sprintf("%d%02d", $row['class_year'], $row['class_name']);
		$ss_id  = $row['ss_id'];

		$studentSql="select student_sn from stud_base where curr_class_num like '$class_sn%'and stud_study_cond='0'";

	}


	$query = "SELECT * FROM score_ss WHERE ss_id=$ss_id";

	$row = $CONN->Execute($query)->fetchRow() or die($query);
	$scope_id= $row['scope_id'];
	$subject_id = $row['subject_id'];
	if ($row['semester'] == 1) {
		$year = $row['year']-1;
		$semester = 2;
		$class_year = $row['class_year']-1;
	}
	else {
		$year = $row['year'];
		$semester = 1;
		$class_year = $row['class_year'];
	}
	
	// 查詢上學期的 ss_id
	$query = "SELECT ss_id FROM score_ss WHERE year=$year AND semester=$semester
	 AND scope_id=$scope_id AND subject_id=$subject_id AND class_year=$class_year AND enable='1'";
	$row = $CONN->Execute($query)->fetchRow();
	
	$query = "SELECT student_sn,ss_score,ss_score_memo FROM stud_seme_score WHERE ss_id={$row['ss_id']} AND
		student_sn IN ($studentSql)";


	$rows =  $CONN->Execute($query)->getAll();
	$arr = array();

	foreach($rows as $row) {
		$arr[$row['student_sn']] = iconv("Big5","UTF-8",$row['ss_score_memo']);
	}

	header("Content-type: text/html; charset=utf-8");
	$returnJson=$json->encode($arr);

	//$returnJson=json_encode($arr);
	echo $returnJson;
}
