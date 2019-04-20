<?php
// $Id: stud_age_explode.php 5310 2009-01-10 07:57:56Z hami $

/* 學務系統設定檔 */
include "stud_query_config.php";  

//認證檢查
sfs_check();

if (!isset($curr_year)) $curr_year =  curr_year();
$sdate=$_GET[sdate];
$edate=$_GET[edate];

$smarty->assign("sex_arr",array("1"=>"男","2"=>"女"));
$smarty->assign("class_arr",class_base());

$query = "select *,left(curr_class_num,length(curr_class_num)-2) as stud_class,right(curr_class_num,2) as stud_site from stud_base where (stud_study_cond=0 or stud_study_cond=15) and stud_birthday >= '$sdate' and stud_birthday <= '$edate' order by ".$_GET[order];
$res = $CONN->Execute($query) or die ($query);
$smarty->assign("data_arr",$res->GetRows());
$smarty->display("stud_query_stud_age_explode.tpl");
?>