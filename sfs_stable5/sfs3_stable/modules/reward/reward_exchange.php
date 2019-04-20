<?php
//$Id: reward_stud_all.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_dataarray.php";

//認證
sfs_check();

//刪除資料
if ($_POST['act']=="del") {
	$query="select student_sn from reward where reward_id='".$_POST['reward_id']."'";
	$res=$CONN->Execute($query);
	if ($res->fields[student_sn]==$_POST['student_sn']) {
		$CONN->Execute("delete from reward where reward_id='".$_POST['reward_id']."'");
		cal_rew($_POST['sel_year'],$_POST['sel_seme'],$_POST['stud_id']);
	}
}

//以輸入學號方式查詢
if ($_POST['id'] && $_POST['stud_id']) {
	$where="a.stud_id='".$_POST['stud_id']."'";
}

//以輸入班級座號方式查詢
if ($_POST['year_name'] && $_POST['class_num'] && $_POST['site_num'] && $_POST['num']) {
	$year_name=$IS_JHORES+$_POST['year_name'];
	$seme_class=$year_name.sprintf("%02d",$_POST['class_num']);
	$seme_num=sprintf("%02d",$_POST['site_num']);
	$where="a.seme_class='$seme_class' and a.seme_num='$seme_num'";
}

//取出學生名單
if ($where !="") {
	$query="select distinct a.student_sn,b.stud_name,b.stud_sex,b.stud_study_cond,b.stud_study_year from stud_seme a,stud_base b where $where and a.student_sn=b.student_sn order by b.stud_study_year desc";
	$res=$CONN->Execute($query);
	$smarty->assign("stud_nums",$res->RecordCount());
	$smarty->assign("stud_rows",$res->GetRows());
}

//以學生流水號處理資料
if ($_POST['student_sn']) {
	$sn=$_POST['student_sn'];
	$query="select * from stud_base where student_sn='$sn'";
	$res=$CONN->Execute($query);
	$_POST['stud_id']=$res->fields['stud_id'];
	$smarty->assign("stud_base",$res->FetchRow());
	//if ($_POST['list']) $sub_kind="and reward_div = '".$_POST['list']."'";
	if ($_POST['only_this']) $seme_sel="and reward_year_seme='".curr_year().curr_seme()."'";
	$query="select * from reward_exchange where student_sn='$sn' $sub_kind $seme_sel order by reward_date desc";
	$res=$CONN->Execute($query);
	$smarty->assign("reward_rows",$res->GetRows());
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","轉學生期中獎懲匯入記錄");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("study_cond",study_cond());
//$smarty->assign("reward_kind",$reward_arr);
if ($_POST['print']) {
	for($i=1;$i<=6;$i++) $f[$i]=0;
	$smarty->assign("f",$f);
	$smarty->assign("school_base",get_school_base());
	$smarty->display("reward_exchange_print.tpl");
} else 
	$smarty->display("reward_exchange.tpl");
?>
