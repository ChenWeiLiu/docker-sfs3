<?php
//$Id: out.php 9240 2018-05-04 03:25:04Z igogo $
include "config.php";

//認證
sfs_check();

if (empty($_POST['year_seme'])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST['year_seme']);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
if ($_POST['sel_stud_id'] && $_POST['add_id']) {
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and stud_id='".$_POST['sel_stud_id']."'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
} elseif ($_POST['sel_year_name'] && $_POST['sel_class_num'] && $_POST['sel_site_num'] && $_POST['add_num']) {
	$_POST['sel_year_name']=intval($_POST['sel_year_name']);
	if ($_POST['sel_year_name']<$IS_JHORES) $_POST['sel_year_name']+=$IS_JHORES;
	$query="select * from stud_seme where seme_year_seme='$seme_year_seme' and seme_class='".$_POST['sel_year_name'].sprintf("%02d",intval($_POST['sel_class_num']))."' and seme_num='".intval($_POST['sel_site_num'])."'";
	$res=$CONN->Execute($query);
	$student_sn=$res->fields['student_sn'];
}
if ($student_sn) {
	$query="replace into score_manage_out (year,semester,student_sn,reason) values ('$sel_year','$sel_seme','$student_sn','".$_POST['reason']."')";
	$res=$CONN->Execute($query);
}
$_POST['del']=intval($_POST['del']);
if ($_POST['del']) {
	$query="delete from score_manage_out where year='$sel_year' and semester='$sel_seme' and student_sn='".$_POST['del']."'";
	$res=$CONN->Execute($query);
}

$query="select * from score_manage_out where year='$sel_year' and semester='$sel_seme'";
$res=$CONN->Execute($query);
$temp_arr=array();
while(!$res->EOF) {
	$temp_arr[]=$res->fields['student_sn'];
	$r_arr[$res->fields['student_sn']]=$res->fields['reason'];
	$res->MoveNext();
}

if (count($temp_arr)>0) {
	$temp_str="'".implode("','",$temp_arr)."'";
	$query="select a.*,b.stud_name,b.stud_sex from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.student_sn in ($temp_str) order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$temp_arr[$sn]['stud_id']=$res->fields['stud_id'];
		$temp_arr[$sn]['seme_class']=$res->fields['seme_class'];
		$temp_arr[$sn]['seme_num']=$res->fields['seme_num'];
		$temp_arr[$sn]['stud_name']=$res->fields['stud_name'];
		$temp_arr[$sn]['stud_sex']=$res->fields['stud_sex'];
		$temp_arr[$sn]['reason']=$r_arr[$sn];
		$res->MoveNext();
	}
}
$smarty->assign("rowdata",$temp_arr);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","成績計算排除學生名單列表"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
$smarty->display("score_manage_new_out.tpl");
?>
