<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "./module-upgrade.php";
include_once "./my_fun.php";

//新增時，檢查分組課程設定是否重複了
function check_dob($ss_id,$group_name,$teacher_sn){
	global $CONN;
	$sql="select count(*) from elective_tea where ss_id='$ss_id' and group_name='$group_name' and teacher_sn='$teacher_sn' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$c=$rs->fields[0];
	return $c;
}

//修改時，檢查分組課程設定是否重複了
function check_dob_upd($e_group_id,$ss_id,$group_name,$teacher_sn){
	global $CONN;
	$sql="select group_id from elective_tea where ss_id='$ss_id' and group_name='$group_name' and teacher_sn='$teacher_sn' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$group_id=$rs->fields[0];
	$c=$rs->RecordCount();
	if($group_id==$e_group_id) $c--;
	return $c;
}

//找出該分組目前有多少學生
function now_mem($group_id){
	global $CONN;
	$sql="select count(*) from elective_stu where group_id='$group_id' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$c=$rs->fields[0];
	return $c;
}

//檢查分組人數是否已經到達上限了
function over_mem($group_id){
	global $CONN;
	//上限人數
	$sql="select member from elective_tea where group_id='$group_id' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$over=$rs->fields['member'];
	if($over==0) {
		$on=999999;
		return $on;//沒有上限
	}
	//目前人數
	$now=now_mem($group_id);
	$on=$over-$now;
	return $on;
}

//看看該生是否早已加入
function is_mem($student_sn,$group_id){
	global $CONN;
	$sql_p="select elective_stu_sn from elective_stu where student_sn='$student_sn' and group_id='$group_id'";
	$rs_p=$CONN->Execute($sql_p) or trigger_error($sql_p,256);
	if($rs_p->fields['elective_stu_sn']) return 1;
	else return 0;
}

//取出該分組可選修人數
function member($group_id){
	global $CONN;
	//上限人數
	$sql="select member from elective_tea where group_id='$group_id' ";
	$rs=$CONN->Execute($sql) or trigger_error($sql,256);
	$member=$rs->fields['member'];
	return $member;
}
?>
