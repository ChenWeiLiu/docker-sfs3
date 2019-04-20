<?php
//$Id: login_edu_select.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

//認證
sfs_check();

//取出學校代碼
$smarty->assign("sch_id",$SCHOOL_BASE[sch_id]);

$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE);
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("module_name","『定期公務報表』網路填報作業登入");
$smarty->assign("SFS_MENU",$school_menu_p);
$smarty->display("edu_chart_login_edu_select.tpl");
?>
