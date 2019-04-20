<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
include "../../include/config.php";
include "../../include/sfs_case_PLlib.php";
require_once "./module-cfg.php";

/* 上傳檔案暫存目錄 */
$path_str = "temp/teacher/score/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;

?>
