<?php

// $Id: config.php 6879 2012-09-10 02:20:13Z infodaes $
require "../../include/config.php";
require "../../include/sfs_case_score.php";
include_once "../../include/sfs_case_PLlib.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once "../../include/sfs_case_dataarray.php";

//---------------------------------------------------
// 這裡請引入您自己的函式庫
//---------------------------------------------------
include_once "my_fun.php";
include_once "../score_input/myfun2.php";

$menu_p = array(
	"class.php"=>"班級階段成績", 
	"person_seme_input.php"=>"個人學期成績");

?>
