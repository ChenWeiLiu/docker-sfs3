<?php
//$Id$
include_once "config.php";

//認證
sfs_check();

//秀出網頁布景標頭
head("校園MSN");
//主選單設定
//$school_menu_p=(empty($school_menu_p))?array():$school_menu_p;

//if ($SFS_IS_CENTER_VER) {
//	$MSN_WINDOW="modules/school_msn/main_index.php?sch_id=".substr($PREFIX_PATH,0,strlen($PREFIX_PATH)-1);
//} else {
  $MSN_WINDOW="modules/school_msn/main_index.php";
//}

//主要內容
 $smarty->assign("SFS_TEMPLATE",$SFS_TEMPLATE); //sfs路徑 
 $smarty->assign("SFS_PATH_HTML",$SFS_PATH_HTML); //sfs HTML
 $smarty->assign("SFS_MENU",$MODULE_MENU); //選單變數
 $smarty->assign("MSN_WINDOW",$MSN_WINDOW); //新視窗URL

 $smarty->display('msn_index.tpl'); 


//佈景結尾
foot();
?>
