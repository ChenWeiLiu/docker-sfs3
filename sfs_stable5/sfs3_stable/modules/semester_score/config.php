<?php
// $Id: config.php 6401 2011-03-30 05:50:15Z infodaes $

/*入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

//引入函數
include "./my_fun.php";
//選單
$menu_p = array("scope_tol.php"=>"領域總表","scope_filter.php"=>"名單篩選","scope_warning.php"=>"修業示警名單篩選","scope_no_pass.php"=>"歷來領域成績不及格名單","scope_no_pass_total.php"=>"各領域成績不及格人數統計表");


//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
?>
