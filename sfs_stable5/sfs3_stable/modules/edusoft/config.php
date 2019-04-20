<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

require_once "./module-cfg.php";

include_once "../../include/config.php";

include_once ("../../include/sfs_case_PLlib.php");

//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

// 類別資料庫名稱
$mastertable = "softm" ;

// 軟體資料庫名稱
$subtable = "soft";

//目錄內程式
$menu_p = array("index.php"=>$ap_name."表列", "tapem_list.php"=>"*類別管理","tape_list.php"=>"*".$ap_name."管理",sfs_check_stu()=>"*".$ap_name."授權管理","tapem_showall.php"=>"*列出全部".$ap_name);
?>
