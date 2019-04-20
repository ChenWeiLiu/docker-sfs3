<?php
// $Id: school_base_config.php 5310 2009-01-10 07:57:56Z hami $

// 載入設定檔
include_once "../../include/config.php" ;
// 公用函式
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_oo_overlib.php";
//檢查更新指令
include_once "module-upgrade.php";

//設定簽章檔上傳路徑
$filePath = set_upload_path("school/title_img");
//目錄內程式
$school_menu_p = array("index.php"=>"基本資料","school_room.php"=>"處室資料","school_title.php"=>"職稱資料");
?>
