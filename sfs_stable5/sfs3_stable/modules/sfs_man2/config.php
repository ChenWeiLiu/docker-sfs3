<?php
// $Id: config.php 6104 2010-09-08 03:44:46Z infodaes $

// 引入 SFS 設定檔，它會幫您載入 SFS 的核心函式庫
include_once "../../include/config.php";

include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_sql.php";
include_once "../../include/sfs_case_studclass.php";
include_once "./module-cfg.php";
include_once "./module-upgrade.php";
include_once "function.php";

$m_arr = &get_module_setup("sfs_man2");
extract($m_arr,EXTR_OVERWRITE);

?>
