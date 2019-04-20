<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

include_once "../../include/config.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_calendar.php";
include_once "../../include/sfs_case_dataarray.php";

include_once "module-cfg.php";

//檢查更新指令
include_once "module-upgrade.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include "score_function.php";
//匯入九年一貫的課程預設陣列
include_once "array.php";
?>
