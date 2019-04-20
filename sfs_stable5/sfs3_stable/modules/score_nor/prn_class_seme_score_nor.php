<?php
//$Id: prn_class_seme_score_nor.php 6646 2011-12-15 01:26:41Z brucelyc $
require_once("config.php");
require_once("chc_class2.php");

//print_r($SCHOOL_BASE);
$sel_class=$_POST[sel_class];
$year_seme = $_POST[year_seme];
$grade = $_POST[grade];
if (empty($year_seme)) $year_seme = sprintf("%03d%d",curr_year(),curr_seme());
if (empty($grade)) $grade = $IS_JHORES+1;
if (!empty($year_seme)) {
	$year=substr($year_seme,0,3);
	$seme=substr($year_seme,-1);
}

//  預設的樣本檔  --(命名：prt列印_ps國小_head表頭.htm)
//$tpl_defult=$template_dir."/chc_prn_week.html";

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

$class_name=sch_class_name($year,$seme,$grade);
$prn_title=sprintf("{$SCHOOL_BASE[sch_cname]}%s學年度第%s學期 日常成績總表 "
	,$year,num_tw($seme));
$break_page="<P STYLE='page-break-before: always;'>";
$smarty->assign('prn_date',date("Y-m-d"));
$smarty->assign('prn_title',$prn_title);

foreach($sel_class as $class_id) {
	$class_data = new data_class($class_id);
	$seme_score_nor = $class_data->seme_score_nor();
	$seme_nor = $class_data->seme_nor();
	$seme_rew = $class_data->seme_rew();
	$seme_absent = $class_data->seme_absent();
	list($year,$seme,$grade,$clano)=explode("_",$class_id);
	$page_title=$prn_title.sprintf("%s年{$class_name[$class_id]}班",num_tw($grade-$IS_JHORES));
	$smarty->assign('subject_nor',$class_data->subject_nor);
	$smarty->assign('subject_abs',$class_data->subject_abs);
	$smarty->assign('subject_rew',$class_data->subject_rew);
	$smarty->assign('subject_score_nor',$class_data->subject_score_nor);

	foreach($class_data->stud_base as $student_sn=>$stud) {
		$class_data->stud_base[$student_sn][seme_score_nor]=$seme_score_nor[$student_sn];
		$class_data->stud_base[$student_sn][seme_nor]=$seme_nor[$student_sn];
		$class_data->stud_base[$student_sn][seme_rew]=$seme_rew[$student_sn];
		$class_data->stud_base[$student_sn][seme_abs]=$seme_absent[$student_sn];
	}
	//print_r($class_data->stud_base);
	++$prn_page;
	$smarty->assign('break_page',($prn_page>1?$break_page:''));
	$smarty->assign('page_title',$page_title);
	$smarty->assign('stud_ary',$class_data->stud_base);
	$smarty->display('class_seme_score_nor.tpl');
//print_r($class_data->stud_base);
	unset($class_data);
}
?>
