<?php
// $Id: chc_report_all.php 5310 2009-01-10 07:57:56Z hami $--
/* 取得設定檔 */

include_once "config.php";

require_once("chc_class_obj.php");
sfs_check();
// smarty的樣版路徑設定  -----------------------------------
$template_dir = $SFS_PATH."/".get_store_path()."/templates";

//  預設的樣本檔  --(命名：prt列印_ps國小_head表頭.htm)
$tpl_defult=$template_dir."/prn_all_absent.html";

//  如果沒有自訂的樣本,就用預設的  --------------------

$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$break_page="<P STYLE='page-break-before: always;'>";
/*
$week_no=$_REQUEST[week_no];
if (empty($week_no)) $week_no=1;
$week_ary = sch_week();
list($sdate,$edate)=explode('/',$week_ary[$week_no]);
for ($i=1; $i<=5; $i++) {
	$week_date[$i]=date_skip($sdate, $i);
}
*/

$week_no=$_REQUEST[week_no];
if (empty($week_no)) $week_no=1;
$week_arr = get_week_arr("","",date("Y-m-d"));
 
$sdate=$week_arr[$week_no];
$edate=date("Y-m-d",strtotime("+6 days",strtotime($sdate)));
for ($i=1; $i<=5; $i++) {
 $week_date[$i]=date_skip($sdate, $i);
}
//$class_ary = sch_class_all();
$class_ary=absent_class($sdate, $edate);
$prn_page = 0;
$stud_ary_all=array();
$prn_title = sprintf("%s學年第%s學期  缺課資料統計 第%d週自 %s 至 %s"
	,Num2CNum(curr_year()),Num2CNum(curr_seme()),$week_no,$sdate,$edate);
foreach($class_ary as $class_id=>$data) {
/*
	print "<pre>";
	print "class_id = $class_id";
	print_r(end($class_ary));
	print "</pre>";
	die;
*/

	list($year,$seme,$grade,$clano)=explode('_',$class_id);
	$grade -= $IS_JHORES;
	$class_data = new data_class($class_id);
	$stud_ary = $class_data->absent_sum($sdate,$edate);
	
	foreach ($stud_ary as $student_sn=>$data) {
		$stud_ary[$student_sn]["GRADE"]=$grade;
		$stud_ary[$student_sn]["CLANO"]=$clano;
		$stud_ary[$student_sn]["W1"]=
			$stud_ary[$student_sn][ABSENT][$week_date[1]];
		$stud_ary[$student_sn]["W2"]=
			$stud_ary[$student_sn][ABSENT][$week_date[2]];
		$stud_ary[$student_sn]["W3"]=
			$stud_ary[$student_sn][ABSENT][$week_date[3]];
		$stud_ary[$student_sn]["W4"]=
			$stud_ary[$student_sn][ABSENT][$week_date[4]];
		$stud_ary[$student_sn]["W5"]=
			$stud_ary[$student_sn][ABSENT][$week_date[5]];
		//---- 合併至全校的陣列	
		$stud_ary_all[$student_sn]=$stud_ary[$student_sn];
	}
	
	
	$page_row=30; //--- 每頁資料筆數
	if (count($stud_ary_all) >= $page_row) {
			$prn_page++;
			$stud_ary = array_slice($stud_ary_all,0,$page_row);
			$stud_ary_all = array_slice($stud_ary_all,$page_row);
			$smarty->assign('break_page',($prn_page>1?$break_page:''));
			$smarty->assign('prn_title',$prn_title);
			$smarty->assign('week_date', $week_date);
			$smarty->assign('stud_ary', $stud_ary);
			$smarty->display("$tpl_defult");
	}
		
	unset($class_data);

}
	//--- 最後一頁
	if (count($stud_ary_all)>0) {
			$prn_page++;
			$stud_ary = array_slice($stud_ary_all,0,$page_row);
			//$stud_ary_all = array_slice($stud_ary_all,$page_row);
			$smarty->assign('break_page',($prn_page>1?$break_page:''));
			$smarty->assign('prn_title',$prn_title);
			$smarty->assign('week_date', $week_date);
			$smarty->assign('stud_ary', $stud_ary);
			$smarty->display("$tpl_defult");
	}

/*
$class_data = new data_class('093_1_07_05');

print_r($class_data);
//print_r($class_data->stud_base());
print_r($class_data->absent_sum('2004-10-24','2004-10-30'));
print "</pre>";
*/

?>