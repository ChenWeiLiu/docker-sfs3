<?php

	// $Id: stud_reg_config.php 9166 2017-11-07 03:53:02Z tuheng $

	//系統設定檔
	include_once "../../include/config.php";
	include_once "../../include/sfs_case_PLlib.php";
	include_once "../../include/sfs_case_dataarray.php";

	//取得模組設定
	$m_arr = get_sfs_module_set("stud_eduh");
	extract($m_arr, EXTR_OVERWRITE);
	$m_arr = get_sfs_module_set();
	extract($m_arr, EXTR_OVERWRITE);
	
	$talk_length=$talk_length?$talk_length:50;

	//檢查是否為內部 IP
	if ($home_ip && !check_home_ip())	{
		header("Location: ".$SFS_PATH_HTML."err_home_ip.php");
	}

	// 學生照片存放的主要目錄
	$img_path="photo/student";
	$img_width = 120;
	
	//選項顯示行數
	$chk_cols = 5;
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
	//新增按鈕名稱
	$sameBtn = "同 戶籍地址";
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

	$eduh_input_method=$eduh_input_method?'stud_eduh_list.php':'stud_eduh_list_2.php';

	//目錄內程式
	$menu_p = array("stud_list.php"=>"基本資料","chi_edit2.php"=>"速編","stud_dom1.php"=>"戶口資料","stud_ext_data.php"=>"補充資料","stud_bs.php"=>"兄弟姐妹","stud_kinfolk.php"=>"其他親屬","basisdata_check.php"=>"基本資料檢查","$eduh_input_method"=>"學期輔導","stud_seme_talk2.php"=>"輔導訪談","stud_seme_spe2.php"=>"特殊表現","stud_psy_test.php"=>"心測記錄","stud_report1.php"=>"學籍記錄表","index2_html.php"=>"輔導記錄表","chc_940531.php"=>"歷屆成績","score_query.php"=>"歷史階段成績查詢");

	function stud_class_err() {
		
		echo "<center><h2>本項作業須具導師資格</h2>";
		echo "<h3>若有疑問請洽 系統管理員</h3></center>";
	}	

?>
