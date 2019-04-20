<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php"; 
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_studclass.php";
include_once "module-cfg.php";
//include_once "../../include/sfs_case_subjectscore.php";

/* 上傳檔案暫存目錄 */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;

//設定資料
$sec=array("升　旗","第一節","第二節","第三節","第四節","第五節","第六節","第七節","降　旗");
$sec_id=array("0"=>"uf","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"df");
$abs_kind=array("C"=>"曠課","V"=>"事假","S"=>"病假","M"=>"喪假","B"=>"公假");
$reward_kind=array("1"=>"警告","2"=>"小過","3"=>"大過","4"=>"嘉獎","5"=>"小","6"=>"大");
$c_times=array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","7"=>"七","8"=>"八","9"=>"九","10"=>"十");
?>
