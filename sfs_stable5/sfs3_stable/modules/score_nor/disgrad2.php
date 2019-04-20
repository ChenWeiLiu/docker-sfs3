<?php
//$Id: disgrad2.php 8492 2015-08-19 12:53:57Z brucelyc $
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
if (intval($_POST['semeday'])==0) {
	if ($sday==0)
		$_POST['semeday']=40;
	else
		$_POST['semeday']=$sday;
}
if (intval($_POST['semeday2'])==0) {
	if ($sday2==0)
		$_POST['semeday2']=234;
	else
		$_POST['semeday2']=$sday2;
}
if ($_POST[year_name]) {
	$seme_year_seme=sprintf("%03d",$sel_year).$sel_seme;
	$seme_class=$_POST[year_name]."%";
	$class_base=class_base($seme_year_seme);
	$query="select a.* from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.seme_class like '$seme_class' and b.stud_study_cond in ('0','15') order by a.seme_num";
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
			$last_seme=sprintf("%03d",$stud_study_year+$i).$j;
			$semes[]=$last_seme;
			$show_year[]=$stud_study_year+$i;
			$show_seme[]=$j;
		}
	}
	if ($_POST[years]!="6" && $_POST[years]!="12") {
		array_pop($semes);
		array_pop($show_year);
		array_pop($show_seme);
	}
	$item=intval($_POST['item']);
	if ($item>1) $item=0;
	$fin_score=count_nor($sn,$semes,$item);
	$all_sn="'".implode("','",array_keys($fin_score))."'";
	$query="select a.*,b.stud_name from stud_seme a left join stud_base b on a.student_sn=b.student_sn where a.seme_year_seme='$seme_year_seme' and a.student_sn in ($all_sn) order by a.seme_class,a.seme_num";
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

if ($_POST['chk3']) $_POST['chk2']=1;
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
for($i=1;$i<=$_POST['years'];$i++) $y[$i]=$_POST['sday'][$i];
$smarty->assign("y",$y);
$smarty->display('score_nor_disgrad2.tpl'); 
?>
