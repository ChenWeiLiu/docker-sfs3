<?php

// $Id: influenza.php 9240 2018-05-04 03:25:04Z igogo $

// 取得設定檔
include "config.php";

sfs_check();

if ($_POST['act']=="sure") {
	$sym_str="";
	if (count($_POST['sym'])>0) foreach($_POST['sym'] as $k=>$v) $sym_str.=$k."@@@";
	$oth_chk_str="";
	if (count($_POST['oth_chk'])>0) foreach($_POST['oth_chk'] as $k=>$v) $oth_chk_str.=$k."@@@";
	$oth_txt_str="";
	if (count($_POST['oth_txt'])>0) foreach($_POST['oth_txt'] as $k=>$v) $oth_txt_str.=$k."###".nl2br($v)."@@@";
	$query="replace into health_disease_report (dis_date,student_sn,dis_kind,status,sym_str,diag_date,diag_hos,diag_name,chk_date,chk_report,oth_chk,oth_txt,teacher_sn) values ('".$_POST['inf_date']."','".$_POST['student_sn']."',1,'".$_POST['status']."','".$sym_str."','".$_POST['diag_date']."','".$_POST['diag_hos']."','".$_POST['diag_name']."','".$_POST['chk_date']."','".$_POST['chk_report']."','".$oth_chk_str."','".$oth_txt_str."','".$_SESSION['session_tea_sn']."')";
	$CONN->Execute($query);
	$dd=getdate(strtotime($_POST['inf_date']));
	if ($dd['wday']>=1 && $dd['wday']<=5) {
		$query="replace into health_inflection_record (student_sn,iid,dis_date,weekday,status,rmemo,teacher_sn) values ('".$_POST['student_sn']."','1','".$_POST['inf_date']."','".$dd['wday']."','".$_POST['status']."','".nl2br(trim($_POST['oth_txt'][1]))."','".$_SESSION['session_tea_sn']."')";
		$CONN->Execute($query);
	}
} elseif ($_POST['act']=="del" && $_POST['student_sn'] && $_POST['dis_date']) {
	$query="delete from health_disease_report where student_sn='".$_POST['student_sn']."' and dis_date='".$_POST['dis_date']."'";
	$CONN->Execute($query);
	$query="delete from health_inflection_record where student_sn='".$_POST['student_sn']."' and dis_date='".$_POST['dis_date']."'";
	$CONN->Execute($query);
}

if ($_POST['act']=="edit" && $_POST['student_sn'] && $_POST['dis_date']) {
	$query="select * from health_disease_report where student_sn='".$_POST['student_sn']."' and dis_date='".$_POST['dis_date']."'";
	$res=$CONN->Execute($query);
	$smarty->assign("rowdata",$res->FetchRow());
} elseif ($_POST['act']!="add") {
	$seme_year_seme=sprintf("%03d",curr_year()).curr_seme();
	$query="select a.*,b.stud_name,b.stud_sex,b.stud_birthday from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$class_num' order by a.seme_num";
	$res=$CONN->Execute($query);
	$temp_arr=array();
	while(!$res->EOF) {
		$sn=$res->fields['student_sn'];
		$sn_arr[]=$sn;
		$temp_arr[$sn]['seme_num']=$res->fields['seme_num'];
		$temp_arr[$sn]['stud_name']=$res->fields['stud_name'];
		$temp_arr[$sn]['stud_sex']=($res->fields['stud_sex']==1)?"男":"女";
		$y_arr=explode("-",$res->fields['stud_birthday']);
		$temp_arr[$sn]['stud_birthyear']=$y_arr[0]-1911;
		$res->MoveNext();
	}
	if (count($sn_arr)>0) {
		$sn_str="'".implode("','",$sn_arr)."'";
		$query="select * from health_disease_report where student_sn in ($sn_str) order by dis_date,student_sn";
		$res=$CONN->Execute($query);
		$smarty->assign("rowdata",$res->GetRows());
		$smarty->assign("studdata",$temp_arr);
		$smarty->assign("status",array("A"=>"生病仍上課","B"=>"生病在家休息","C"=>"生病住院"));
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","流感登錄");
$smarty->assign("SFS_MENU",$menu_p);
if ($_POST['add']) {
	$smarty->assign("stud_menu",stud_menu(curr_year(),curr_seme(),substr($class_num,0,-2),substr($class_num,-2,2),$_POST['student_sn']));
	$smarty->display("class_health_influenza_form.tpl");
} elseif ($_POST['act']=="edit") {
	$query="select * from stud_base where student_sn='".$_POST['student_sn']."'";
	$res=$CONN->Execute($query);
	$smarty->assign("stud_menu","<span style=\"color:".(($res->fields['stud_sex']==1)?"blue":"red").";\">".$res->fields['stud_name']."</span> &nbsp; (出生年次: ".(substr($res->fields['stud_birthday'],0,4)-1911).")");
	$smarty->display("class_health_influenza_form.tpl");
} else
	$smarty->display("class_health_influenza.tpl");
?>
