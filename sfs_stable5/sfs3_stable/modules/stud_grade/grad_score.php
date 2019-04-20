<?php
//$Id: grad_score.php 5491 2009-06-04 06:38:19Z infodaes $
include "config.php";
include "../../include/sfs_case_score.php";

//認證
sfs_check();

if($_POST[show_rank]) $show_rank='Checked';

if ($_POST[check]) header("Location: ".$SFS_PATH_HTML."modules/stud_query/check_score_error3.php");

if($IS_JHORES) {
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
} else {
	$ss_link=array("語文-本國語文"=>"chinese","語文-鄉土語文"=>"local","語文-英語"=>"english","數學"=>"math","生活"=>"life","自然與生活科技"=>"nature","社會"=>"social","健康與體育"=>"health","藝術與人文"=>"art","綜合活動"=>"complex");
	$link_ss=array("chinese"=>"語文-本國語文","local"=>"語文-鄉土語文","english"=>"語文-英語","math"=>"數學","life"=>"生活","nature"=>"自然與生活科技","social"=>"社會","health"=>"健康與體育","art"=>"藝術與人文","complex"=>"綜合活動");
}
if (empty($_POST[year_seme])) {
	$sel_year = curr_year(); //目前學年
	$sel_seme = curr_seme(); //目前學期
	$year_seme=$sel_year."_".$sel_seme;
} else {
	$ys=explode("_",$_POST[year_seme]);
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
	if ($IS_JHORES==0)
		$f_year=5;
	else
		$f_year=2;
	for ($i=0;$i<=$f_year;$i++) {
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
	$fin_score=cal_fin_score($sn,$semes,"",array($sel_year,$sel_seme,$_POST[year_name]));
	
//echo "<PRE>";
//print_r($fin_score);
//echo "<PRE>";
	
	$fin_nor_score=cal_fin_nor_score($sn,$semes);
	$sm=&get_all_setup("",$sel_year,$sel_seme,$_POST[year_name]);
	$rule=explode("\n",$sm[rule]);
	while(list($s,$v)=each($fin_score)) {
		$fin_score[$s][avg][str]=score2str($fin_score[$s][avg][score],"",$rule);
	}
	reset($sn);
	while(list($s,$v)=each($sn)) {
		if ($_POST[grad_score]) {
			$query="select * from grad_stud where stud_grad_year='$sel_year' and stud_id='$stud_id[$s]'";
			$res=$CONN->Execute($query);
			$gsn=$res->fields[grad_sn];
			if ($gsn!="")
				$query="update grad_stud set grad_score='".$fin_score[$sn[$s]][avg][score]."' where grad_sn='$gsn'";
			else
				$query="insert into grad_stud (stud_grad_year,class_year,class_sort,stud_id,grad_score) values ('$sel_year','$_POST[year_name]','$_POST[me]','$stud_id[$s]','".$fin_score[$sn[$s]][avg][score]."')";
			$CONN->Execute($query);
		}
	}
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
$smarty->assign("stud_num",count($stud_id));
$smarty->assign("fin_score",$fin_score);
$smarty->assign("fin_nor_score",$fin_nor_score);
$smarty->assign("ss_link",$ss_link);
$smarty->assign("link_ss",$link_ss);
$smarty->assign("ss_num",count($ss_link));
$smarty->assign("rule",$rule_all);
$smarty->assign("jos",$IS_JHORES);
$smarty->assign("class_base",class_base($seme_year_seme));
$smarty->assign("seme_class",$_POST[year_name].sprintf("%02d",$_POST[me]));
$smarty->assign("show_rank",$show_rank);
if ($_POST[friendly_print]) {
	$smarty->display("stud_grad_grad_score_print.tpl");
} else {
	if ($show_rank) $smarty->display("stud_grad_score_rank.tpl");
		else $smarty->display("stud_grad_grad_score.tpl");
}
?>
