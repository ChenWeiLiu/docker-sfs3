<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
 

	//模組更新程式
	require_once "./module-upgrade.php";
	  

//載入本模組的專用函式庫
include_once "../../include/sfs_case_studclass.php";
include_once "../../include/sfs_case_subjectscore.php";
include_once ('my_functions.php');

//在學學生編碼 0:在籍, 15:在家自學
$in_study="'0'";
	
?>

