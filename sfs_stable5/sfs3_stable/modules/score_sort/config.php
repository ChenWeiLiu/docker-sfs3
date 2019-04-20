<?php
// $Id: config.php 2015-10-17 22:11:15Z qfon $

/*入學務系統設定檔*/
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";

//引入函數
include "./my_fun.php";
//include "module-upgrade.php";


//選單
$menu_p = array("score_sort.php"=>"補救教學名單","scope_pr.php"=>"各領域PR值趨勢",
	);
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
?>