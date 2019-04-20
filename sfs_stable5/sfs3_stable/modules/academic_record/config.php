<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
include_once "../../include/config.php";
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_subjectscore.php";
include "../../include/sfs_oo_zip2.php";
include "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
require_once "./module-cfg.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include_once "function.php";
include_once "my_function.php";

//檢查更新指令
include_once "module-upgrade.php";

$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
?>
