<?php
// $Id: stud_kind_explode.php 7712 2013-10-23 13:31:11Z smallduh $

/* 學務系統設定檔 */
include "stud_query_config.php";  

//認證檢查
sfs_check();

if (!isset($curr_year)) $curr_year =  curr_year();
$stud_kind=$_REQUEST[stud_kind];

//取得學生身分別代號
$stud_kind_arr = stud_kind();

$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
$smarty->assign("stud_kind",$stud_kind_arr[$stud_kind]);
$smarty->assign("curr_kind",$stud_kind);
$smarty->assign("class_arr",class_base());

if ($stud_kind==0) 
	$query = "select a.*,b.*,left(a.curr_class_num,length(a.curr_class_num)-2) as stud_class,right(a.curr_class_num,2) as stud_site from stud_base a left join stud_domicile b on a.student_sn=b.student_sn where (a.stud_kind like '%,$stud_kind,%' or a.stud_kind =0) and a.stud_study_cond=0 order by a.curr_class_num";
else
	$query = "select a.*,b.*,left(a.curr_class_num,length(a.curr_class_num)-2) as stud_class,right(a.curr_class_num,2) as stud_site from stud_base a left join stud_domicile b on a.student_sn=b.student_sn where (a.stud_kind like '%,$stud_kind,%' or a.stud_kind = $stud_kind) and a.stud_study_cond=0 order by a.curr_class_num";

//echo $stud_kind."|".$query."|<BR>\n";
$res = $CONN->Execute($query) or die ($query);
$smarty->assign("data_arr",$res->GetRows());
if ($_POST[csv_out]) {
	header("Content-disposition: filename=".curr_year()."_".curr_seme()."_".$stud_kind.".csv");
	header("Content-type: application/octetstream ; Charset=Big5");
	//header("Pragma: no-cache");
				//配合 SSL連線時，IE 6,7,8下載有問題，進行修改 
				header("Cache-Control: max-age=0");
				header("Pragma: public");
	header("Expires: 0");
	$smarty->display("stud_query_stud_kind_explode_csv.tpl");
} else
	$smarty->display("stud_query_stud_kind_explode.tpl");
?>
