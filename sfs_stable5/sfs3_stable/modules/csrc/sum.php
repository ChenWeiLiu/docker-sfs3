<?php

// $Id: sum.php 5766 2009-11-25 08:01:31Z brucelyc $

// 取得設定檔
include "config.php";

sfs_check();

//取得週次
$weeks_array=get_week_arr($sel_year,$sel_seme,$_POST[temp_date]);
$sel_week=$_POST['sel_week'];
if ($sel_week) $weeks_array[0]=$sel_week;
if ($weeks_array[0]=="") $weeks_array[0]=1;
$sel_week=$weeks_array[0];
$smarty->assign("weeks_arr",$weeks_array);

if ($_POST['item_id']=="") $_POST['item_id']=1;

$query="select * from csrc_item where sub_id=0 order by main_id";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$item_arr[$res->fields['main_id']]=$res->fields['memo'];
	$res->MoveNext();
}

if ($item_arr[$_POST['item_id']]=="") $_POST['item_id']=1;

$query="select * from csrc_item where main_id='".$_POST['item_id']."' and sub_id<>0 order by sub_id";
$res=$CONN->Execute($query);
while(!$res->EOF) {
	$sub_arr[$res->fields['sub_id']]=$res->fields['memo'];
	$res->MoveNext();
}

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","校安總表");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("item_arr",$item_arr);
$smarty->assign("sub_arr",$sub_arr);
$smarty->assign("class_arr",class_base());
if ($_POST['act']=="add")
	$smarty->display("csrc_add.tpl");
else
	$smarty->display("csrc_sum.tpl");
?>
