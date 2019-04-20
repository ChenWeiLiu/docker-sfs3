<?php
//$Id: five.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_case_menu.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

if (empty($year_seme)) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$year_seme);
	$sel_year=$ys[0];
	$sel_seme=$ys[1];
}

if ($_POST[me]) {
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$_POST[year_name].sprintf("%02d",$_POST[me]);
	$query="select a.*,b.stud_name from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class='$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn[$res->fields[seme_num]]=$res->fields[student_sn];
		$stud_name[$res->fields[seme_num]]=$res->fields[stud_name];
		$stud_id[$res->fields[seme_num]]=$res->fields[stud_id];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".pos($sn)."'";
	$res=$CONN->Execute($query);
	$stud_study_year=$res->fields[0];
	for ($i=0;$i<=2;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			$show_year[]=$stud_study_year+$i;
			$show_seme[]=$j;
		}
	}
	if ($_POST[years]!=6) {
		array_pop($semes);
		array_pop($show_year);
		array_pop($show_seme);
	}
	$fin_score=cal_fin_nor_score($sn,$semes);
}
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","畢業成績表"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("class_name_menu",class_name_menu($sel_year,$sel_seme,$_POST[year_name],$_POST[me])); 
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("semes",$semes);
$smarty->assign("stud_id",$stud_id);
$smarty->assign("student_sn",$sn);
$smarty->assign("stud_name",$stud_name);
$smarty->assign("fin_score",$fin_score);
$smarty->assign("ss_num",count($semes));
$smarty->assign("stud_num",count($sn));
if ($_POST[friendly_print]) {
	$smarty->display('score_nor_five_print.tpl'); 
} else {
	$smarty->display('score_nor_five.tpl'); 
}
?>