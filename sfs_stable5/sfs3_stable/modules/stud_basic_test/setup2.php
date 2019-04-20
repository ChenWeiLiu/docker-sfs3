<?php

// $Id:$

include "select_data_config.php";

sfs_check();

if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
	$_POST[year_seme]=$year_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}
$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
$_POST['year_name']=intval($_POST['year_name']);
if ($_POST['year_name']<1 || $_POST['year_name']>9) $_POST['year_name']=9;

if($_POST['sn']) {
	$query="update stud_seme_dis set su='0' where student_sn='".intval($_POST['sn'])."'";
	$res=$CONN->Execute($query);
}

if ($_POST['stud_str']) {
	$stud_arr=explode("\n",$_POST['stud_str']);
	foreach($stud_arr as $k=>$v) {
		$stud_arr[$k]=trim($v);
		if (strlen($stud_arr[$k])==4) {
			$seme_class=$_POST['year_name'].substr($stud_arr[$k],0,2);
			$seme_num=intval(substr($stud_arr[$k],2,2));
			$query="update stud_seme_dis set su='1' where seme_year_seme='$seme_year_seme' and seme_class='$seme_class' and seme_num='$seme_num'";
			$res=$CONN->Execute($query);
		}
	}
}

$rowdata=array();
$query="select * from stud_seme_dis where seme_year_seme='$seme_year_seme' and su='1' order by seme_class,seme_num";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$sn=$res->fields['student_sn'];
	$query2="select * from stud_base where student_sn='$sn'";
	$res2=$CONN->Execute($query2);
	$rowdata[$sn]=array("stud_id"=>$res2->fields['stud_id'], "class_no"=>substr($res->fields['seme_class'],-2,2), "seme_num"=>$res->fields['seme_num'], "stud_name"=>$res2->fields['stud_name'], "stud_sex"=>$res2->fields['stud_sex']);
	$res->MoveNext();
}
$smarty->assign("rowdata",$rowdata); 

$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","標記直升生"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("work_year",$sel_year+1);
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name]));
$smarty->display("stud_basic_test_setup2.tpl");
?>
