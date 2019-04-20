<?php
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
/* 取得學務系統設定檔 */
include_once "../../include/config.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "module-cfg.php";
include_once "get_data_function.php" ;

//取得模組設
$m_arr = &get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);

?>
