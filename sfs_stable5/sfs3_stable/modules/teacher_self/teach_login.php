<?php
//$Id: teach_login.php 9240 2018-05-04 03:25:04Z igogo $
include "teach_config.php";

//認證
sfs_check();

$query="select * from login_log_new where teacher_sn = '".$_SESSION['session_tea_sn']."' and who = '".$_SESSION['session_who']."' order by no";
$res=$CONN->Execute($query);
$smarty->assign("rowdata",$res->GetRows());

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","個人登入資訊");
$smarty->assign("SFS_MENU",$teach_menu_p);
$smarty->display("teacher_self_teach_login.tpl");
?>
