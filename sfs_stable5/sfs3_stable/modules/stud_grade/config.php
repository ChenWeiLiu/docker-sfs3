<?php
// $Id: config.php 6430 2011-05-06 08:31:27Z hami $

require_once "module-cfg.php";
require_once "my_fun.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include './module-upgrade.php';

//取得模組參數設定
$m_arr = &get_module_setup("stud_grade");
extract($m_arr, EXTR_OVERWRITE);

$PHP_SELF = basename($_SERVER["PHP_SELF"]) ;
?>
