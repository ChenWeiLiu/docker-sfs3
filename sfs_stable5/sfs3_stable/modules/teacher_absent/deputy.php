<?php
//$Id: list.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";
include "../../include/sfs_class_absent.php";

//認證
sfs_check();

$a=new absent("teacher");

if ($_POST[edit]) {
	   foreach($_POST['edit'] as $id=>$val);
		$query="update teacher_absent set status='1',deputy_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
} elseif ($_POST[del]) {
		foreach($_POST['del'] as $id=>$val);
		$query="update teacher_absent set status='0',deputy_date='".date("Y-m-d H:i:s")."' where id='$id'";
		$CONN->Execute($query);
}



$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","職務代理"); 
$smarty->assign("SFS_MENU",$school_menu_p); 




//選擇學期
$smarty->assign("year_seme_menu",year_seme_menu($sel_year,$sel_seme));
//選擇教師
$smarty->assign("leave_teacher_menu",teacher_menu("teacher_sn",$_POST[teacher_sn])); 
//選擇月份
$smarty->assign("month",month_menu($_POST[month])); 

//選擇是否確定
$smarty->assign("d_check4_menu",d_make_menu("是否確定",$_POST[d_check4] , $check_arr,"d_check4",1)); 

$smarty->assign("abs_kind_arr",$a->absent_kind_arr);
$smarty->assign("tea_arr",my_teacher_array());
$smarty->assign("course_kind_arr",$course_kind);
$smarty->assign("status_kind_arr",$status_kind);
$smarty->assign("check1",$check1);
$smarty->assign("check2",$check2);
$smarty->assign("check3",$check3);
$smarty->assign("check4",$check4);
//條件
$query1.=" year='$sel_year' and semester='$sel_seme' ";

if ($_POST[teacher_sn]) {
$query1 .=" and teacher_sn='$_POST[teacher_sn]'";
}
if ( $_POST[month] ) {
$query1 .=" and month='$_POST[month]'";
}

if ($_POST[d_check4]==1) {
	$query1 .=" and check4_sn >0 ";
}else{
	$query1 .=" and check4_sn =0 ";

}

//取得本人請假資料
$query="select * from teacher_absent where deputy_sn=$_SESSION[session_tea_sn] and " .$query1 ;


$query .=" order by start_date  desc ";



$res=$CONN->Execute($query);
$smarty->assign("absent",$res->GetRows());

$smarty->display('deputy.tpl'); 



?>