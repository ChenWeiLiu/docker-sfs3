<?php
//$Id: fix_link.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

$temp_path=$UPLOAD_PATH;
$temp_file=$temp_path."/Module_Path.txt";
if (!$_GET['status']) {
	unlink($temp_file);
	header("Location: ".$_SERVER['PHP_SELF']."?status=OK");
}
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","連結列修正");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("system_fix_link.tpl");
?>
