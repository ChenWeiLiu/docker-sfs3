<?php
// $Id: config.php 5763 2009-11-22 16:36:50Z infodaes $
include_once "../../include/config.php";
require_once "./module-cfg.php";
require_once "../../include/sfs_case_PLlib.php";
require_once "../../include/sfs_case_dataarray.php";

 //取得模組參數的類別設定
$m_arr = &get_module_setup('curriculum_xml');
extract($m_arr,EXTR_OVERWRITE);
?>
