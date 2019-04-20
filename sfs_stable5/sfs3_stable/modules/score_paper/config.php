<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
require_once "./function.php";
include_once "../../include/config.php";
//您可以自己加入引入檔
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_oo_zip2.php";
include_once "../../include/sfs_case_PLlib.php";
include "module-upgrade.php";
?>
