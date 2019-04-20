<?php
//$Id: reward_list.php 8236 2014-12-10 14:14:14Z infodaes $
include "config.php";

sfs_check();

if ($_POST['un']=="") $_POST['un']=1;
$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
$query="select * from stud_seme where seme_year_seme='$seme_year_seme'";
$res=$CONN->Execute($query);
$sn_arr=array();
while(!$res->EOF) {
	$sn_arr[]=$res->fields['student_sn'];
	$res->MoveNext();
}
if (count($sn_arr)>0) {
	$sn_str="'".implode("','",$sn_arr)."'";
	//建立懲戒排序表
	$Create_db="DROP TABLE IF EXISTS reward_top";
	mysql_query($Create_db);
	$Create_db="
		CREATE temporary TABLE reward_top (
		student_sn int(10) unsigned NOT NULL default '0',
		nums int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (student_sn))
	";
	mysql_query($Create_db);
	if ($_POST['un']) $str="and reward_cancel_date='0000-00-00'";
	$query="select student_sn,reward_kind,count(*) as nums from reward where reward_div='2' and student_sn in ($sn_str) $str group by student_sn,reward_kind";
	$res=$CONN->Execute($query);
	$sn_arr=array();
	$rowdata=array();
	$rmapping_arr=array("",6,6,5,5,4,4,4);
	$nmapping_arr=array("",1,2,1,2,1,2,3);
	$sn=0;
	$osn=0;
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$rkind=intval($res->fields['reward_kind'])*(-1);
		$rowdata[$sn][$rmapping_arr[$rkind]]+=$nmapping_arr[$rkind]*$res->fields['nums'];
		$rowdata[$sn]['total']+=$nmapping_arr[$rkind]*$res->fields['nums']*(7-$rmapping_arr[$rkind]);
		$sn_arr[$sn]=1;
		if ($osn!=$sn && $osn!=0) $CONN->Execute("insert into reward_top (student_sn,nums) values ('$osn','".$rowdata[$osn]['total']."')");
		$osn=$sn;
		$res->MoveNext();
	}
	if (count($sn_arr)>0) {
		$studata=array();
		$query="select * from reward_top order by nums desc,student_sn";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$studata[$res->fields['student_sn']]=array();
			$res->MoveNext();
		}
		$ns_arr=array_keys($sn_arr);
		$sn_str="'".implode("','",$ns_arr)."'";
		//$query="select * from stud_base where student_sn in ($sn_str) and stud_study_cond='0' order by curr_class_num";
		$query="select * from stud_base where student_sn in ($sn_str) order by curr_class_num";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$studata[$res->fields['student_sn']]['stud_name']=$res->fields['stud_name'];
			$studata[$res->fields['student_sn']]['stud_sex']=$res->fields['stud_sex'];
			$studata[$res->fields['student_sn']]['class']=substr($res->fields['curr_class_num'],0,-2);
			$studata[$res->fields['student_sn']]['num']=substr($res->fields['curr_class_num'],-2,2);
			$res->MoveNext();
		}
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","懲戒列表");
$smarty->assign("SFS_MENU",$student_menu_p);
$smarty->assign("reward_kind",array("6"=>"警告","5"=>"小過","4"=>"大過"));
$smarty->assign("rowdata",$rowdata);
$smarty->assign("studata",$studata);
$smarty->display("reward_reward_list.tpl");
?>
