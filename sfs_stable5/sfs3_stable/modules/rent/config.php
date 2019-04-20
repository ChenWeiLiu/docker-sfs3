<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once"./module-cfg.php";
include_once"../../include/config.php";
//取得模組參數的類別設定
$m_arr=&get_module_setup("rent");
extract($m_arr,EXTR_OVERWRITE);
$borrower_type=array("public"=>"公","private"=>"私","special"=>"特");
$c_day=array('一','二','三','四','五','六','日');
?>