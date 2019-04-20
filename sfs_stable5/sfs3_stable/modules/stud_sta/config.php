<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z hami $

	//系統設定檔
	include "../../include/config.php";
	//函式庫

	include "../../include/sfs_case_PLlib.php"; 
	include "../../include/sfs_oo_zip2.php";
	include "module-cfg.php";
	include "module-upgrade.php";	
	//年級數字
	$class_year2=array("1"=>"一","2"=>"二","3"=>"三","4"=>"四","5"=>"五","6"=>"六");

	//預設國籍
	$default_country = "中華民國";
	//左選單設定顯示筆數
	$gridRow_num = 16;
	//左選單底色設定
	$gridBgcolor="#DDDDDC";
	//左選單男生顯示顏色
	$gridBoy_color = "blue";
	//左選單女生顯示顏色
	$gridGirl_color = "#FF6633";
	//預設第一個開啟班級 
	$default_begin_class = "601";	
	$postProve="開立證明";

?>
