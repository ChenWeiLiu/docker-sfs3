<?php

// $Id: config.php 7971 2014-04-01 06:50:58Z smallduh $
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_calendar.php";

require_once "./module-cfg.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
//取得模組設定
$m_arr = get_sfs_module_set("calendar");
extract($m_arr, EXTR_OVERWRITE);

?>