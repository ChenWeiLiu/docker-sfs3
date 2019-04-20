<?php
// $Id: config.php 5310 2009-01-10 07:57:56Z hami $
	//系統設定檔
	require_once "../../include/config.php";
	//函式庫
	require_once "../../include/sfs_case_PLlib.php";    
	//新增按鈕名稱
	$newBtn = " 新增資料 ";
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	//刪除按鈕名稱
	$delBtn = " 確定刪除 ";
	//確定新增按鈕名稱
	$postMoveBtn = " 確定異動 ";
	$postInBtn = " 確定轉入 ";
	$postOutBtn = " 確定 ";
	$postReinBtn = " 確定復學 ";
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
	//預設第一個開啟班級 
	$default_begin_class = "601";	
	//類別
	$gb_which_arr= array("獎勵"=>"獎勵","懲戒"=>"懲戒");
	//獎懲類別
	//$gb_kind_arr_g= array("3"=>"大功一次","6"=>"大功二次","9"=>"大功三次","2"=>"小功一次","5"=>"小功二次","8"=>"小功三次","1"=>"嘉獎一次","4"=>"嘉獎二次","7"=>"嘉獎三次");
	//$gb_kind_arr_b= array("-3"=>"大過一次","-6"=>"大過二次","-9"=>"大過三次","-2"=>"小過一次","-5"=>"小過二次","-8"=>"小過三次","-1"=>"警告一次","-4"=>"警告二次","-7"=>"警告三次");
	//$gb_kind_arr= array("3"=>"大功一次","6"=>"大功二次","9"=>"大功三次","2"=>"小功一次","5"=>"小功二次","8"=>"小功三次","1"=>"嘉獎一次","4"=>"嘉獎二次","7"=>"嘉獎三次","-3"=>"大過一次","-6"=>"大過二次","-9"=>"大過三次","-2"=>"小過一次","-5"=>"小過二次","-8"=>"小過三次","-1"=>"警告一次","-4"=>"警告二次","-7"=>"警告三次");
	$gb_kind_arr_g= array("big_good1"=>"大功一次","big_good2"=>"大功二次","big_good3"=>"大功三次","s_good1"=>"小功一次","s_good2"=>"小功二次","s_good3"=>"小功三次","ss_good1"=>"嘉獎一次","ss_good2"=>"嘉獎二次","ss_good3"=>"嘉獎三次");
	$gb_kind_arr_b= array("big_bad1"=>"大過一次","big_bad2"=>"大過二次","big_bad3"=>"大過三次","s_bad1"=>"小過一次","s_bad2"=>"小過二次","s_badd3"=>"小過三次","ss_bad1"=>"警告一次","ss_bad2"=>"警告二次","ss_bad3"=>"警告三次");
	$gb_kind_arr= array("big_good1"=>"大功一次","big_good2"=>"大功二次","big_good3"=>"大功三次","s_good1"=>"小功一次","s_good2"=>"小功二次","s_good3"=>"小功三次","ss_good1"=>"嘉獎一次","ss_good2"=>"嘉獎二次","ss_good3"=>"嘉獎三次","big_bad1"=>"大過一次","big_bad2"=>"大過二次","big_bad3"=>"大過三次","s_bad1"=>"小過一次","s_bad2"=>"小過二次","s_badd3"=>"小過三次","ss_bad1"=>"警告一次","ss_bad2"=>"警告二次","ss_bad3"=>"警告三次");
	//有效積分
	$gb_score_arr = array("1"=>"未銷過","0"=>"已銷過");
	//升降級
	$demote_arr = array("9"=>"升級","10"=>"降級");
	//復學類別
	$rein_arr= array("3"=>"中輟復學","4"=>"休學復學");
	//目錄內程式
	$student_menu_p = array("stud_move.php"=>"轉入","stud_move_out.php"=>"調出","stud_move_rein.php"=>"復學","stud_demote.php"=>"升降級","stud_move_gradu.php"=>"畢業轉出","../stud_reg/"=>"學籍管理");
	// 模組選單
	$menu_p = array("addgood_gb.php"=>"獎勵作業","addbad_gb.php"=>"懲戒作業","gb_score_tools.php"=>"銷過處理","print_gb_list.php"=>"列印通知單");	
?>