<?php
// $Id: chc_prn_week.php 5310 2009-01-10 07:57:56Z hami $
/* 取得設定檔 */
include_once "config.php";
require_once("chc_class_obj.php");
sfs_check();
// smarty的樣版路徑設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates";

//  預設的樣本檔  --(命名：prt列印_ps國小_head表頭.htm)
$tpl_defult=$template_dir."/chc_prn_week.html";

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";

$edate = date("Y-m-d");
//-- 94/01/07 修正
$week_arr = get_week_arr("","",$edate);
foreach ($week_arr as $week_no=>$data) {
	if ($week_no==0) continue;
	$week_ary[$week_no]="第{$week_no}週 ({$data} ~ ".date("Y-m-d",strtotime("+6 days",strtotime($data))).")";
}
$smarty->assign('week_ary',$week_ary);
head('缺課週報表');
print_menu($school_menu_p);
$smarty->display($tpl_defult); 
foot();
?>
