<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_book.php";

//將必要變數引入smarty
$smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE."/book");
$smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML);
$smarty->assign("BOOK_URL",$SFS_PATH_HTML."templates/$SFS_THEME/book");
$smarty->assign("menu_p",$menu_p);
?>
