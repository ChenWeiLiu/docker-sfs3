<?php
//$Id: disgrad.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
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

$show_ss=array("language"=>"語文","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
if ($_POST[year_name]) {
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$class_base=class_base($seme_year_seme);
	$seme_class=$_POST[year_name]."%";
	$query="select a.student_sn from stud_seme a right join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' and b.stud_study_cond in ('0','15')";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$sn[]=$res->fields[student_sn];
		$res->MoveNext();
	}
	$query="select stud_study_year from stud_base where student_sn='".$sn[0]."'";
	$res=$CONN->Execute($query);
	$stud_study_year=$res->fields[0];
	for ($i=0;$i<=2;$i++) {
		for ($j=1;$j<=2;$j++) {
			$semes[]=sprintf("%03d",$stud_study_year+$i).$j;
			$show_year[]=$stud_study_year+$i;
			$show_seme[]=$j;
		}
	}
	if ($_POST[years]!="6" && $_POST[years]!="12") {
		array_pop($semes);
		array_pop($show_year);
		array_pop($show_seme);
	}
	$fin_score=cal_fin_score($sn,$semes,$_POST[fail_num]);
	$all_sn="'".implode("','",array_keys($fin_score))."'";
	$query="select a.*,b.stud_name from stud_seme a right join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.student_sn in ($all_sn) order by a.seme_class,a.seme_num";
	$res=$CONN->Execute($query);
	while(!$res->EOF) {
		$ssn=$res->fields[student_sn];
		$show_sn[$ssn]=$ssn;
		$sclass[$ssn]=$class_base[$res->fields[seme_class]];
		$snum[$ssn]=$res->fields[seme_num];
		$stud_name[$ssn]=$res->fields[stud_name];
		$stud_id[$ssn]=$res->fields[stud_id];
		$res->MoveNext();
	}
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","修業建議名單"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme)); 
$smarty->assign("class_year_menu",class_year_menu($sel_year,$sel_seme,$_POST[year_name])); 
$smarty->assign("show_year",$show_year);
$smarty->assign("show_seme",$show_seme);
$smarty->assign("show_ss",$show_ss);
$smarty->assign("show_ss_num",count($show_ss));
$smarty->assign("semes",$semes);
$smarty->assign("show_sn",$show_sn);
$smarty->assign("stud_id",$stud_id);
$smarty->assign("stud_name",$stud_name);
$smarty->assign("sclass",$sclass);
$smarty->assign("snum",$snum);
$smarty->assign("fin_score",$fin_score);
$smarty->assign("fin_score_num",count($fin_score));
$smarty->assign("seme_class",$_POST[year_name].sprintf("%02d",$_POST[me]));
if ($_POST[friendly_print]) {
	$smarty->display("score_manage_new_disgrad_print.tpl");
} else {
	$smarty->display("score_manage_new_disgrad.tpl");
}
?>