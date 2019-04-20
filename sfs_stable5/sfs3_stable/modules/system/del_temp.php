<?php
//$Id: del_temp.php 5590 2009-08-18 01:40:05Z brucelyc $
include "config.php";

//認證
sfs_check();

if ($_POST['clean']) $smarty->clear_compiled_tpl();

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","刪除網頁暫存檔");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->assign("rowdata",$rowdata);
$smarty->display("system_del_temp.tpl");
?>
