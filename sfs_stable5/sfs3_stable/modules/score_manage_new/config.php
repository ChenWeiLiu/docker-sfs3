<?php
// $Id: config.php 8977 2016-09-19 07:49:18Z infodaes $

/*入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

//引入函數
include "./my_fun.php";
include "module-upgrade.php";

//選單
$menu_p = array("score_query.php"=>"成績查詢",
		"top.php"=>"階段成績(定期)優異排名",
		"avg.php"=>"定期評量各班平均",
		"manage.php"=>"成績繳交狀況(年級課程)",
		"manage_class.php"=>"成績繳交狀況(班級課程)",
		"manage_ele.php"=>"分組班繳交狀況",
		"tol.php"=>"分科總表",
		"scope_tol.php"=>"領域總表",
		"check.php"=>"空白成績檢查",
		"check100.php"=>"百分成績檢查",
		"out.php"=>"排除名單");
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
?>