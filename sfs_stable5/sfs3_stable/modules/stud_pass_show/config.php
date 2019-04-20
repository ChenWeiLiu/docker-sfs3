<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

//系統設定檔
include_once "../../include/config.php";
//函式庫
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
include_once "../../include/sfs_case_studclass.php";
require_once "./module-cfg.php";

$menu_p = array("stud_pass_list.php"=>"全校學生密碼一覽");

//目前學年學期
$this_seme_year_seme = sprintf("%03d%d",curr_year(),curr_seme());

?>
