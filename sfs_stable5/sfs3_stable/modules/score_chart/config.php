<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
include_once "../../include/config.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include "../../include/sfs_oo_zip2.php";
include "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include_once "function.php";
include_once "my_function.php";

//檢查更新指令
include_once "module-upgrade.php";

?>
