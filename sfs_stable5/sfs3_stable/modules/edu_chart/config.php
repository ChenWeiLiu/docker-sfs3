<?php
//$Id: config.php 5310 2009-01-10 07:57:56Z hami $
//預設的引入檔，不可移除。
require_once "./module-cfg.php";
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
require_once "./my_fun.php";

//上傳檔案暫存目錄 
$path_str = "temp/edu_chart/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;
?>
