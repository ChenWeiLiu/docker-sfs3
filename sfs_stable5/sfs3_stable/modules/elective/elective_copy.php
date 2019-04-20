<?php

// $Id: elective_copy.php 8512 2015-09-02 01:44:17Z smallduh $

// 取得設定檔
include "config.php";

sfs_check();

$sel_year=curr_year();
$sel_seme=curr_seme();

if ($_POST['curr']=="") $_POST['curr']=1;
if ($_POST[copy] && $_POST[sel_group]) {
	$query="select * from elective_stu where group_id='$_POST[sel_group]' order by elective_stu_sn";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$CONN->Execute("insert into elective_stu (group_id,student_sn) values ('$_POST[group_id]','".$res->fields[student_sn]."')");
		$res->MoveNext();
	}
}

if ($_POST[clear]) {
	$query="delete from elective_stu where group_id='$_POST[group_id]'";
	$res=$CONN->Execute($query);
}

if ($_POST[c_year]) {
	//年級科目選單
	$class=array($sel_year,$sel_seme,"",$_POST[c_year]);
	$ss_name_arr=&get_ss_name_arr($class,$mode="長");
	if ($ss_name_arr=="")
		$smarty->assign("subject_menu","本學期的課程尚未設定");
	else
		$smarty->assign("subject_menu",subject_menu($ss_name_arr,$_POST[ss_id]));

	//教師資料
	$tea_arr=teacher_base();
	$smarty->assign("tea_arr",$tea_arr);

	//分組班人數
	$query="select group_id,count(student_sn) as num from elective_stu group by group_id";
	$res=$CONN->Execute($query);
	$rowdata2=array();
	while(!$res->EOF) {
		$rowdata2[$res->fields[group_id]]=$res->FetchRow();
	}
	$smarty->assign("stu_num",$rowdata2);

	//分組班資料
	if ($_POST[curr]) $c_str="where b.year='".curr_year()."' and b.semester='".curr_seme()."'";
	$query="select a.*,b.year,b.semester,b.class_year from elective_tea a left join score_ss b on a.ss_id=b.ss_id $c_str order by b.year,b.semester,a.ss_id,a.group_id";
	$res=$CONN->Execute($query);
	$class_arr=array();
	$rowdata=array();
	while(!$res->EOF) {
		if ($res->fields[ss_id]==$_POST[ss_id] && $res->fields[year]==$sel_year && $res->fields[semester]==$sel_seme) $class_arr[$res->fields[group_id]]=$res->fields[group_name]." -- ".$tea_arr[$res->fields[teacher_sn]]." ( ".intval($rowdata2[$res->fields[group_id]][num])." / ".$res->fields[member]." )";
		if ($res->fields[group_id]==$_POST[group_id])
			$res->MoveNext();
		else
			$rowdata[]=$res->FetchRow();
	}
	$smarty->assign("rowdata",$rowdata);
	$smarty->assign("class_menu",class_menu($class_arr,$_POST[group_id]));
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","分組課程設定");
$smarty->assign("SFS_MENU",$menu_p);
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[c_year]));
$smarty->display("elective_elective_copy.tpl");
?>
