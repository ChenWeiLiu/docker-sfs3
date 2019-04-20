<?php

// $Id: config.php 6674 2012-01-18 03:40:07Z infodaes $

	//系統設定檔
	include_once "../../include/config.php";
	//函式庫
	include_once "../../include/sfs_case_PLlib.php";
	include_once "../../include/sfs_case_dataarray.php";
	include_once "../../include/sfs_case_studclass.php";
	
	//新增按鈕名稱
	$sameBtn = "同 戶籍地址";
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
	//預設第一個開啟年級 
	$default_begin_class = 6;
	//照片寬度
	$img_width = 120;
	
	//目錄內程式
	$menu_p = array("stud_base.php"=>"基本資料","stud_dom1.php"=>"戶口資料","stud_bs.php"=>"兄弟姐妹","stud_kinfolk.php"=>"其他親屬","basisdata_check.php"=>"學籍基本資料完整性檢查");
	
	//設定上傳檔案路徑
	$img_path = "photo/student";
	$upload_str = set_upload_path("$img_path");
	
?>
