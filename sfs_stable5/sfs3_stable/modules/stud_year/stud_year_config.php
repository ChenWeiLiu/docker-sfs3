<?php

// $Id: stud_year_config.php 5978 2010-08-10 08:47:23Z brucelyc $

	//系統設定檔
	include "../../include/config.php";
	//函式庫
	include "../../include/sfs_case_PLlib.php";

	//函式庫
	include "../../include/sfs_case_studclass.php";



	//新增按鈕名稱
	$newBtn = " 新增資料 ";
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	//刪除按鈕名稱
	$delBtn = " 確定刪除 ";
	//確定新增按鈕名稱
	$postBtn = " 確定新增 ";
	$editModeBtn = " 修改模式 ";
	$browseModeBtn = " 瀏覽模式 ";
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


	//目錄內程式
	$menu_p = array("stud_year_1.php"=>"學期編班",
	"elps_rand_class.php"=>"亂數編班",
	"stud_year_2.php"=>"同年級間班級調整",
	"stud_site_num.php"=>"學生座號管理",
	"class_site_num.php"=>"班級座號管理",
	"quick_num_edit.php"=>"座號速編",
	"class_import.php"=>"編班資料匯入",
	"stud_compare.php"=>'學期座號對應',
	"view_1.php"=>"學籍查核1",
	"view_2.php"=>"學籍查核2");


