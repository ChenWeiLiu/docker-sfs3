<?php
//$Id: readme.php 5310 2009-01-10 07:57:56Z hami $
include "config.php";

$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML); 
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); 
$smarty->assign("module_name","畢業生作業說明"); 
$smarty->assign("SFS_MENU",$menu_p); 
$smarty->display("stud_grad_readme.tpl");
?>