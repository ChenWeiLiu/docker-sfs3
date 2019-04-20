<?php

// $Id: inflection.php 9240 2018-05-04 03:25:04Z igogo $

// 取得設定檔
include "config.php";

sfs_check();

$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
//計算本週日期
$dd=getdate();
for($i=1;$i<=5;$i++) $weekday_arr[$i]=date("Y-m-d",(time()+86400*($i-$dd['wday'])));

//確定新增資料
if ($_POST['act']=="sure") {
	$sval=0;
	if (count($_POST['id'])>0) foreach($_POST['id'] as $v) {
		$query="delete from health_inflection_record where id='$v'";
		$CONN->Execute($query);
	}
	foreach($_POST['status'] as $k=>$v) {
		if ($v!="" && in_array($k,$weekday_arr)) {
			$sval=1;
			$weekday=array_search($k,$weekday_arr);
			$query="replace into health_inflection_record (student_sn,iid,dis_date,weekday,status,rmemo,teacher_sn) values ('".$_POST['student_sn']."','".$_POST['iid']."','$k','$weekday','$v','".nl2br(trim($_POST['rmemo']))."','".$_SESSION['session_tea_sn']."')";
			$res=$CONN->Execute($query);
		}
	}
}

//確定刪除資料
if ($_POST['act']=="del" && $_POST['iid'] && $_POST['student_sn']) {
	$query="delete from health_inflection_record where student_sn='".$_POST['student_sn']."' and iid='".$_POST['iid']."' and dis_date>='".$weekday_arr[1]."' and dis_date<='".$weekday_arr[5]."'";
	$res=$CONN->Execute($query);
}

if ($_POST['act']=="edit" && $_POST['student_sn'] && $_POST['iid']) {
	$query="select * from health_inflection_record where student_sn='".$_POST['student_sn']."' and iid='".$_POST['iid']."' and dis_date>='".$weekday_arr[1]."' and dis_date<='".$weekday_arr[5]."'";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$temp_arr['weekday'][$res->fields['weekday']]=$res->fields['status'];
		$temp_arr['id'][]=$res->fields['id'];
		$res->MoveNext();
	}
	$smarty->assign("rowdata",$temp_arr);
} else {
	$query="select a.*,b.stud_name,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_num' and b.stud_study_cond='0'";
	$res=$CONN->Execute($query);
	$temp_sn=array();
	$temp_std=array();
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$temp_sn[]=$sn;
		$temp_std[$sn]['stud_name']=$res->fields['stud_name'];
		$temp_std[$sn]['stud_sex']=$res->fields['stud_sex'];
		$res->MoveNext();
	}
	if (count($temp_sn)>0) $sn_str="'".implode("','",$temp_sn)."'";
	$query="select a.*,b.seme_num from health_inflection_record a left join stud_seme b on a.student_sn=b.student_sn where b.seme_year_seme='$seme_year_seme' and a.student_sn in ($sn_str) and a.dis_date>='".$weekday_arr[1]."' and a.dis_date<='".$weekday_arr[5]."' order by b.seme_num,a.dis_date";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$temp_arr[$sn][$res->fields['iid']]['stud_name']=$temp_std[$sn]['stud_name'];
		$temp_arr[$sn][$res->fields['iid']]['stud_sex']=$temp_std[$sn]['stud_sex'];
		$temp_arr[$sn][$res->fields['iid']]['seme_num']=$res->fields['seme_num'];
		$temp_arr[$sn][$res->fields['iid']][$res->fields['weekday']]=$res->fields['status'];
		$res->MoveNext();
	}
	$smarty->assign("rowdata",$temp_arr);
}

//取得通報項目
$query="select * from health_inflection_item where enable='1' order by iid";
$res=$CONN->Execute($query);
$smarty->assign("inf_arr",$res->GetRows());
	
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","流感登錄");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("cweekday",array("1"=>"週一","2"=>"週二","3"=>"週三","4"=>"週四","5"=>"週五"));
$smarty->assign("weekday_arr",$weekday_arr);
if ($_POST['add']) {
	$smarty->assign("stud_menu",stud_menu(curr_year(),curr_seme(),substr($class_num,0,-2),substr($class_num,-2,2),$_POST['student_sn']));
	$smarty->display("class_health_inflection_form.tpl");
} elseif ($_POST['act']=="edit") {
	$query="select * from stud_base where student_sn='".$_POST['student_sn']."'";
	$res=$CONN->Execute($query);
	$smarty->assign("stud_menu","<span style=\"color:".(($res->fields['stud_sex']==1)?"blue":"red").";\">".$res->fields['stud_name']."</span>");
	$smarty->display("class_health_inflection_form.tpl");
} else
	$smarty->display("class_health_inflection.tpl");
?>
