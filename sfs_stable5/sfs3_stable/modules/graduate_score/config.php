<?php
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
require_once "module-cfg.php";
require_once "my_fun.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";

//取得模組參數設定
$m_arr = &get_module_setup("graduate_score");
extract($m_arr,EXTR_OVERWRITE);
$semesters=explode(',',$m_arr['semesters']);

$PHP_SELF = basename($_SERVER["PHP_SELF"]) ;
?>
