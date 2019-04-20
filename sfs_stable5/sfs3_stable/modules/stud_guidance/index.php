<?php
//$Id: index.php 7420 2013-08-09 23:58:53Z infodaes $
include_once "config.php";
sfs_check();
alter_table();

if ($_POST[act]=='write'){
	if ($_POST[c_from]=='0' ||$_POST[c_from]=='')  backe("轉介來源未填！");
	if ($_POST[tea_sn]=='0' ||$_POST[tea_sn]=='' )  backe("認輔教師未選擇！");
	if ($_POST[c_kind]=='' || $_POST[c_kind]=='0')  backe("問題類別未填！");
	if ($_POST[c_bdate]=='')  backe("接案日期未填！");
	if ($_POST[c_isover]=='1' && $_POST[end_date]=='' )  backe("結案日期未填！");
	($_POST[c_isover]=='0') ? $end_date='0000-00-00':$end_date=$_POST[end_date];
	$now_t=date("Y-m-d H:i:s");
	$c_kind=join(',',$_POST[c_kind]);
	$SQL="update stud_guid set  guid_c_from='$_POST[c_from]',begin_date='$_POST[c_bdate]',guid_tea_sn ='$_POST[tea_sn]',guid_c_kind='$c_kind',guid_c_isover='$_POST[c_isover]',guid_over_reason='$_POST[guid_over_reason]',update_time='$now_t',end_date='$end_date',update_id='$_SESSION[session_tea_sn]' where guid_c_id='$_POST[guid_c_id]'";
	$rs=$CONN->Execute($SQL) or die($SQL);
	$URL=$_SERVER[PHP_SELF]."?view=".$_POST[view];
	header("Location:$URL");
}
if ($_GET[del_id]!=''){
	$SQLa="delete from stud_guid where guid_c_id ='$_GET[del_id]' ";
	$SQLb="delete from stud_guid_event where guid_c_id ='$_GET[del_id]' ";
	$rs=$CONN->Execute($SQLa) or die($SQLa);
	$rs=$CONN->Execute($SQLb) or die($SQLb);
	$URL=$_SERVER[PHP_SELF]."?view=".$_POST[view];
	header("Location:$URL");
}

($_GET[view]!='') ? $view=$_GET[view]:$view='now';
//取目前學期
($_GET[Seme]!='') ? $Seme=$_GET[Seme]:$Seme=sprintf("%03d",curr_year()).curr_seme();
($_GET[page]!='') ? $page=$_GET[page]:$page=0;

## ---- 計算筆數 ------###

	$SQL1="select  guid_c_id from stud_guid where guid_c_isover=0 ";
	$SQL2="select  guid_c_id from stud_guid where guid_c_isover=1 ";
	($_GET[view]=='old')? $SQL=$SQL2: $SQL=$SQL1;
	$rs=$CONN->Execute($SQL) or die($SQL);
	$total=  ceil($rs->RecordCount()/  $size);//總頁數
$page_link='頁:';
for ($i=0;$i<$total;$i++){
($i==$page) ? $page_link.="<U>".($i+1)."</U>&nbsp;":$page_link.="<a href='$_SERVER[PHP_SELF]?page=$i&view=$view'>".($i+1)."</a>&nbsp;";

}





$template_dir = $SFS_PATH."/".get_store_path()."/templates/";
$tpl_file=$template_dir."ind.htm";
$smarty->left_delimiter="{{";
$smarty->right_delimiter="}}";
// ($Sclass) ? $LINK=link_a($Seme,$Sclass): $LINK=link_a($Seme);
$SEX=array(1=>"<img src=images/boy.gif height=25>",2=>"<img src=images/girl.gif height=25>");
$stud_coud=study_cond();
$sch_data=get_school_base();
head("個別輔導紀錄");
print_menu($school_menu_p);
myheader();
//$stud_list=get_stu_list($Seme);
$stud_list=get_stu_list3($view,$page);


//print_r($stud_s);
	$sel_tea=get_tea_sel();
	$smarty->assign("PHP_SELF",$_SERVER[PHP_SELF]);
	$smarty->assign("page_link",$page_link);
	$smarty->assign("page",$page);
	$smarty->assign("view",$view);
	$smarty->assign("stud_list", $stud_list );
	$smarty->assign("sel_tea",$sel_tea);//教師下拉選單
	$smarty->assign("c_from",$come_from);//教師下拉選單
	$smarty->assign("question_kind",$question_kind);//教師下拉選單
	$smarty->assign("c_isover",$guid_over);//教師下拉選單

	$all_tea=get_tea_data(1);
	$smarty->assign("teach",$all_tea);
	$smarty->display($tpl_file);

foot();
?>
