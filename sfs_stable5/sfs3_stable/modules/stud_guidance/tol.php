<?php
//$Id: tol.php 5310 2009-01-10 07:57:56Z hami $
require_once"config.php";
sfs_check();
$stu_nu=get_date_seme();
//echo "<pre>";
///計算該學期接輔數
foreach($stu_nu as $key=>$data){
$SQL="select count(guid_c_id) as begin_num from stud_guid where  begin_date BETWEEN '$data[start]' AND '$data[end]'  ";
$rs = $CONN->Execute($SQL) or die($SQL);
$ary=$rs->GetArray();
$stu_nu[$key][begin_num]=$ary[0][begin_num];
}
///累計至該學期未結案數
////1.找本期接輔。2.本學期結案。3.於本學期前接案,其他學期結案。 4.於本學期前接案,目前仍未結案。
foreach($stu_nu as $key=>$data){
	$SQL="select  COUNT(DISTINCT guid_c_id) as tol_num from stud_guid where  (begin_date  BETWEEN '$data[start]' AND '$data[end]' ) or (end_date  BETWEEN '$data[start]' AND '$data[end]' ) or (begin_date <'$data[start]' and end_date > '$data[end]' ) or (begin_date <'$data[start]' and guid_c_isover='0' ) ";
	
	$rs = $CONN->Execute($SQL) or die($SQL);
	$ary=$rs->GetArray();
	$stu_nu[$key][tol_num]=$ary[0][tol_num];
}

////該學期認輔教師數
foreach($stu_nu as $key=>$data){
	$SQL="select COUNT(DISTINCT guid_tea_sn) as tea_num from stud_guid  where (begin_date  BETWEEN '$data[start]' AND '$data[end]' ) or (end_date  BETWEEN '$data[start]' AND '$data[end]' ) or (begin_date <'$data[start]' and end_date > '$data[end]' ) or (begin_date <'$data[start]' and guid_c_isover='0' )";
	////1.找本期接輔。2.本學期結案。3.於本學期前接案,其他學期結案。 4.於本學期前接案,目前仍未結案。
	$rs = $CONN->Execute($SQL) or die($SQL);
	$ary=$rs->GetArray();
	$stu_nu[$key][tea_num]=$ary[0][tea_num];
}

////該學期認輔完成數
foreach($stu_nu as $key=>$data){
	$SQL="select count(guid_c_id) as end_num from stud_guid where ( end_date BETWEEN '$data[start]' AND '$data[end]')  and  guid_c_isover='1' ";
	$rs = $CONN->Execute($SQL) or die($SQL);
	$ary=$rs->GetArray();
	$stu_nu[$key][end_num]=$ary[0][end_num];
}
###########################################################
//print_r($stu_nu);
$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$tpl_file=$template_dir."tol.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
$SEX=array(1=>"<img src=images/boy.gif height=25>",2=>"<img src=images/girl.gif height=25>");
head("個別輔導紀錄");
print_menu($school_menu_p);
myheader();
$smarty->assign("stu_nu", $stu_nu);
$smarty->display($tpl_file);

?>
