<?php
	// $Id: teach_config.php 6772 2012-05-29 08:09:16Z brucelyc $
	include_once "../../include/config.php";
	include_once "../../include/sfs_case_PLlib.php";
	include_once "../../include/sfs_case_dataarray.php";
	
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	
	//設定上傳圖片路徑
	$img_path = "photo/teacher";
	
	//照片寬度
	$img_width = 120;
	
	//目錄內程式
	$teach_menu_p = array("teach_list.php"=>"基本資料","teach_connect.php"=>"網路資料","teach_login.php"=>"登入資料","../chpass/teach_cpass.php"=>"更改密碼","cdc.php"=>"自然人憑證註冊");
?>
