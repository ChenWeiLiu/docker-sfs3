<?php
//$Id: prt.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();
//您可以自己加入引入檔
require_once("chc_class2.php");

//程式檔頭
head("日常成績管理");
print_menu($menu_p);

// smarty的樣版路徑設定  -----------------------------------
//$template_dir = $SFS_PATH."/".get_store_path()."/templates";

//  預設的樣本檔  --(命名：prt列印_ps國小_head表頭.htm)
//$tpl_defult=$template_dir."/chc_prn_week.html";

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

$year_seme_ary=year_seme_ary();
$grade_ary=array("7"=>"一年級","8"=>"二年級","9"=>"三年級");
if ($IS_JHORES < 6) $grade_ary=array("1"=>"一年級","2"=>"二年級","3"=>"三年級","4"=>"四年級","5"=>"五年級","6"=>"六年級");

$sort_ary=array('1'=>'第一次','2'=>'第二次','3'=>'第三次');


$year_seme = $_POST[year_seme];
$grade = $_POST[grade];
$class_id = $_POST[class_id];
$test_sort=$_POST[test_sort];
if (empty($test_sort)) $test_sort=1;
if (empty($year_seme)) $year_seme = sprintf("%03d%d",curr_year(),curr_seme());
if (empty($grade)) $grade = $IS_JHORES+1;
if (!empty($year_seme)) {
	$year=substr($year_seme,0,3);
	$seme=substr($year_seme,-1);
}
if (empty($class_id)) $class_id=sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,1);

$class_ary = sch_class_name($year,$seme,$grade);
if (!empty($class_id) and empty($class_ary[$class_id])) $class_id=sprintf("%03d_%d_%02d_%02d",$year,$seme,$grade,1);



//主要內容
$class_id = sprintf("%03d_%d_%02d_01",$year,$seme,$grade);
$sub_ary = get_subj($class_id,'stage');
$prn_ary=array('prn_stud_seme_score.php'=>'學期成績通知單'
	,'prn_class_seme_score.php'=>'學期成績班級總表'
	,'prn_class_seme_score_nor.php'=>'日常成績班級總表'
	,'test.php'=>'test');

//-- 94/01/14 修正 
//---- 將 $class_ary 數量補足為 10 的倍數 使版面整齊
while (count($class_ary) % 10 != 0) $class_ary[count($class_ary)]='';
//--- 94/01/14 修正 結束 -------------------------------------------
$smarty->assign('year_seme_ary',$year_seme_ary);
$smarty->assign('year_seme',$year_seme);
$smarty->assign('grade_ary',$grade_ary);
$smarty->assign('grade',$grade);
$smarty->assign('class_ary',$class_ary);
$smarty->assign('class_id',$class_id);
$smarty->assign('prn_ary',$prn_ary);
$smarty->display("sel_class.tpl");
//unset($class_data);

//print "<pre>";
//print_r(score_add($class_id,$test_sort));
//print_r($sub_ary);
/*
print_r($year_seme_ary);
print_r($class_ary);
print "<br> $year--$seme--$grade--$year_seme ";
print_r($_POST);
*/
//print "</pre>";

//佈景結尾
foot();


?>
