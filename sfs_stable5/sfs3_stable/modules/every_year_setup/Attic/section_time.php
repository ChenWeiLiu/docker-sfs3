<?php
//$Id: section_time.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_PLlib.php";
include "my_fun.php";

//認證
sfs_check();

if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."-".$sel_seme;
} else {
	$ys=explode("-",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if ($_POST[save]) {
	$st=$_POST[st];
	while(list($k,$v)=each($st)) {
		$stime=trim($st[$k][0])."-".trim($st[$k][1]);
		if ($stime!="-") {
			$query="replace into section_time (year,semester,sector,stime) values ('$sel_year','$sel_seme','$k','$stime')";
			$CONN->Execute($query);
		}
	}
	$_POST[act]="show";
}

if ($_POST[act]) {
	$section_table=section_table($sel_year,$sel_seme);
}

while(list($i,$v)=each($section_table)) {
	reset($v);
	$err=0;
	while(list($j,$vv)=each($v)) {
		if ($tnow!="" && $tnow>=$section_table[$i][$j]) $err=1;
		if ($section_table[$i][$j]!="") $tnow=$section_table[$i][$j];
	}
	$bg[$i]=($err==1)?"#FF0000":"#FFFFFF";
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","各節時間設定"); 
$smarty->assign("SFS_MENU",$school_menu_p); 
//$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("year_seme_menu", class_ok_setup_year($sel_year,$sel_seme,"year_seme") ) ;
$smarty->assign("sel_year",$sel_year);
$smarty->assign("sel_seme",$sel_seme);
$smarty->assign("section_table",$section_table);
$smarty->assign("bg",$bg);
$smarty->display('every_year_setup_section_time.tpl'); 
?>