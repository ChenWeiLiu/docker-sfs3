<?php
//$Id: reward_stud_all.php 6920 2012-10-01 08:25:16Z infodaes $
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

//取得學年學期
$year_seme=$_REQUEST[year_seme];
if ($year_seme) {
	$sel_year=intval(substr($year_seme,0,3));
	$sel_seme=substr($year_seme,3,1);
} else {
	$sel_year=(empty($_REQUEST[sel_year]))?curr_year():$_REQUEST[sel_year];
	$sel_seme=(empty($_REQUEST[sel_seme]))?curr_seme():$_REQUEST[sel_seme];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;

//學年學期選單
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$year_seme_p=get_class_seme();
	$year_seme_select = "<select name='year_seme' onchange='this.form.submit()';>\n";
	while (list($k,$v)=each($year_seme_p)){
		if ($seme_year_seme==$k)
	      		$year_seme_select.="<option value='$k' selected>$v</option>\n";
	      	else
	      		$year_seme_select.="<option value='$k'>$v</option>\n";
	}
	$year_seme_select.= "</select>"; 

	
//年級與班級選單
$default_class_id=($IS_JHORES+1).'01';
$class_id=$_POST[class_id]?$_POST[class_id]:$default_class_id;
$_POST['stud_id']=$_POST['stud_id_select']?$_POST['stud_id_select']:$_POST['stud_id'];
$class_select=&classSelect($sel_year,$sel_seme,"","class_id",$class_id,true);
$stud_select=get_stud_select($class_id,$One,"stud_id_select","",1);
$_POST['stud_id_select']='';

//以輸入學號方式查詢
if ($_POST['id'] && $_POST['stud_id']) {
	$stud_id=$_POST['stud_id'];
	$where="(a.stud_id='$stud_id' or b.stud_person_id='$stud_id')";
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
	$res=$CONN->Execute($query) or die($query);
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
	if ($_POST['list']) $sub_kind="and reward_div = '".$_POST['list']."'";
	if ($_POST['only_this']) $seme_sel="and reward_year_seme='".curr_year().curr_seme()."'";
	$cancel=($_POST['cancel_chk'])?"":"and reward_cancel_date='0000-00-00'";
	$query="select * from reward where student_sn='$sn' $sub_kind $cancel $seme_sel order by reward_div,reward_date desc";
	$res=$CONN->Execute($query);
	$smarty->assign("reward_rows",$res->GetRows());
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","學生獎懲明細");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("study_cond",study_cond());
$smarty->assign("reward_kind",$reward_arr);
$smarty->assign("year_seme_select",$year_seme_select);
$smarty->assign("stud_select",$stud_select);
$smarty->assign("class_select",$class_select);

if ($_POST['print']) {
	for($i=1;$i<=6;$i++) $f[$i]=0;
	$smarty->assign("f",$f);
	$smarty->assign("school_base",get_school_base());
	$smarty->display("reward_reward_stud_all_print.tpl");
} else 
	$smarty->display("reward_reward_stud_all.tpl");
?>
