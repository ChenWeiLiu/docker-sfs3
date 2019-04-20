<?php

// $Id: config.php 8859 2016-03-29 06:09:44Z qfon $
require "../../include/config.php";

require "module-cfg.php";
include_once "../../include/sfs_case_PLlib.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include_once "my_fun.php";
/* 上傳檔案暫存目錄 */
$path_str = "temp/student/";
set_upload_path($path_str);
$temp_path = $UPLOAD_PATH.$path_str;
//取得模組設定
$m_arr = get_sfs_module_set();
extract($m_arr, EXTR_OVERWRITE);
$section_include=$section_include?$section_include:'1,2,3,4,5,6,7';
//設定資料
$sec=array("升　旗","第一節","第二節","第三節","第四節","第五節","第六節","第七節","降　旗");
$sec_id=array("0"=>"uf","1"=>"1","2"=>"2","3"=>"3","4"=>"4","5"=>"5","6"=>"6","7"=>"7","8"=>"df");
$abs_kind=array("C"=>"曠課","V"=>"事假","S"=>"病假","M"=>"喪假","B"=>"公假");
$reward_kind=array("1"=>"警告","2"=>"小過","3"=>"大過","4"=>"嘉獎","5"=>"小功\","6"=>"大功\");
$c_times=array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六","7"=>"七","8"=>"八","9"=>"九","10"=>"十");
$nor_val=array("1"=>"表現優異","2"=>"表現良好","3"=>"表現尚可","4"=>"需再加油","5"=>"有待改進");
$nor_kind=array("10"=>"1","9"=>"1","8"=>"2","7"=>"2","6"=>"3","5"=>"3","4"=>"3","3"=>"4","2"=>"4","1"=>"5","0"=>"5");
?>
