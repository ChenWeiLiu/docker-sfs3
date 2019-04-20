<?php
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
require_once "./module-cfg.php";
require "../../include/config.php";
require "../../include/sfs_case_PLlib.php";
require "../../include/sfs_oo_zip2.php";
require_once "./module-upgrade.php";
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);		
	
$menu_p = array(
"newstud_input.php"=>"匯入新生", 
"newstud_manage.php"=>"管理新生",
"newstud_compile.php"=>"臨時編班",
"newstud_notification.php"=>"入學通知單",
"auto_compile.php"=>"正式編班",
"set_id.php"=>"設定學號",
"real_input.php"=>"寫入學籍表", 
"print_paper.php"=>"報表列印",
"chc_940601.php"=>"資料匯出入<br>(彰縣用)",
"tcc.php"=>"資料匯出入<br>(中縣用)",
"readme.php"=>"使用說明");
?>
