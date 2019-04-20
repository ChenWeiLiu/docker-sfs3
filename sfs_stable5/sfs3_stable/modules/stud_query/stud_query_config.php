<?php

// $Id: stud_query_config.php 6034 2010-08-25 15:00:40Z wkb $
include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_dataarray.php";
//選單
$menu_p = array("stud_status2.php"=>"人數統計",
"all_year.php"=>"歷年總表",
"sum.php"=>"異動表",
"stud_age.php"=>"年齡統計",
"stud_birth.php"=>"生日月份統計",
"stud_star.php"=>"星座統計",
"stud_kind_rep.php"=>"身分統計",
"check_error2.php"=>"學籍成績檢查");

$sex_arr = array("1"=>"男","2"=>"女");
$class_base = class_base();
?>
