<?php
//$Id:$
include "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("module_name","模組說明");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("index.html");
?>