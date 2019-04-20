<?php

// $Id: config.php 5310 2009-01-10 07:57:56Z smallduh $

	//系統設定檔
	include_once "./module-cfg.php";
	include_once "../../include/config.php";
	//函式庫
	include "../../include/sfs_case_PLlib.php"; 
	
	require_once "./module-upgrade.php";
	  
  require_once "./my_functions.php";
  
  //取得模組設定
$m_arr = get_sfs_module_set("stud_eduh");
extract($m_arr, EXTR_OVERWRITE);
$m_arr = get_sfs_module_set("stud_class");
extract($m_arr, EXTR_OVERWRITE);

//預設第一個開啟年級
//$default_begin_class = 6;
//左選單設定顯示筆數
$gridRow_num = 16;
//左選單底色設定
$gridBgcolor="#DDDDDC";
//左選單男生顯示顏色
$gridBoy_color = "blue";
//左選單女生顯示顏色
$gridGirl_color = "#FF6633";
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
?>
