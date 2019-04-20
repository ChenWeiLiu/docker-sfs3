<?php

// $Id: module-cfg.php 8914 2016-06-22 03:35:53Z qfon $

// 資料表名稱定義

$MODULE_TABLE_NAME[0] = "stud_absent";
$MODULE_PRO_KIND_NAME = "學生出缺勤管理";

// 模組最後更新版本
$MODULE_UPDATE_VER="1.0";

// 模組最後更新日期
$MODULE_UPDATE="2003-04-24";

// 是否為系統模組? 若設為 1 則該模組不可刪除
$SYS_MODULE=1;

//---------------------------------------------------
// 4. 這裡請定義：您這支程式需要用到的：變數或常數
//---------------------------------------------------

$today=date("Y-m-d");
//目錄內程式
$school_menu_p = array(
"index.php"=>"缺曠課登記",
"index_group.php"=>"團體登記",
"stat.php"=>"缺曠課明細",
"stat_all.php"=>"缺曠課統計",
"add_record.php"=>"學期缺曠課登記",
"add_record_person.php"=>"個人學期缺曠課補登",
"report.php"=>"缺曠課報表",
"chc_prn_week.php"=>"網頁式缺課週報表",
"week_abs_tol.php"=>"本週概況",
"semester_rank.php"=>"高風險學生"
);

//---------------------------------------------------
//
// 5. 這裡定義：預設值要由 "模組參數管理" 程式來控管者，
//    若不想，可不必設定。
//
// 格式： var 代表變數名稱
//       msg 代表顯示訊息
//       value 代表變數設定值
//
// 若您決定將這些變數交由 "模組參數管理" 來控管，那麼您的模組程式
// 就要對這些變數有感知，也就是說：若這些變數值在模組參數管理中改變，
// 您的模組就要針對這些變數有不同的動作反映。
//
// 例如：某留言板模組，提供每頁顯示筆數的控制，如下：
// $SFS_MODULE_SETUP[1] =
// array('var'=>"PAGENUM", 'msg'=>"每頁顯示筆數", 'value'=>10);
//
// 上述的意思是說：您定義了一個變數 PAGENUM，這個變數的預設值為 10
// PAGENUM 的中文名稱為 "每頁顯示筆數"，這個變數在安裝模組時會寫入
// pro_module 這個 table 中
//
// 我們有提供一個函式 get_module_setup
// 供您取用目前這個變數的最新狀況值，
//
// 詳情請參考 include/sfs_core_module.php 中的說明。
//
// 這區的 "變數名稱 $SFS_MODULE_SETUP" 請勿改變!!!
//---------------------------------------------------


$SFS_TEXT_SETUP[] = array(
"g_id"=>1,
"var"=>"缺曠課類別",
"s_arr"=>array("曠課","事假","病假","喪假","公假","不可抗力")
);
$SFS_MODULE_SETUP[0] = array("var"=>"report_line","msg"=>"列印週報表時每頁筆數","value"=>"13");
$SFS_MODULE_SETUP[1] = array("var"=>"default_uf","msg"=>"預設整天含升旗","value"=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[2] = array("var"=>"default_df","msg"=>"預設整天含降旗","value"=>array("1"=>"是","0"=>"否"));
$SFS_MODULE_SETUP[3] = array("var"=>"ranks","msg"=>"排名列表人數","value"=>50);

?>
