<?php

// $Id: config.php 8832 2016-03-03 08:08:13Z brucelyc $

	//系統設定檔
	include "../../include/config.php";
	//函式庫
	include "../../include/sfs_case_PLlib.php"; 
	include  "../../include/sfs_oo_date.php";
	include_once "../../include/sfs_case_calendar.php";
	include_once "../../include/sfs_case_studclass.php";
	include "./my_fun.php";
	require_once "./module-upgrade.php";
	  
	//新增按鈕名稱
	$newBtn = " 新增資料 ";
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	//刪除按鈕名稱
	$delBtn = " 確定刪除 ";
	//確定新增按鈕名稱
	$postMoveBtn = " 確定異動 ";
	$postInBtn = " 確定轉入 ";
	$postOutBtn = " 確定獎勵 ";
	$postOutBtn2 = " 確定懲戒 ";
	$postOutBtn3 = "銷過";
	$postReinBtn = " 確定復學 ";
	$editModeBtn = " 修改模式 ";
	$browseModeBtn = " 瀏覽模式 ";
	$postcancel = "銷過";	
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
	$default_begin_class = 1+$IS_JHORES."01";	
	//獎勵類別
	$reward_good_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次");
	$reward_bad_arr=array("-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
	$reward_arr=array("1"=>"嘉獎一次","2"=>"嘉獎二次","3"=>"小功一次","4"=>"小功二次","5"=>"大功一次","6"=>"大功二次","7"=>"大功三次","-1"=>"警告一次","-2"=>"警告二次","-3"=>"小過一次","-4"=>"小過二次","-5"=>"大過一次","-6"=>"大過二次","-7"=>"大過三次");
	//目錄內程式
	$student_menu_p = array("reward_one.php"=>"在籍生個人獎懲登記","reward_entrance.php"=>"轉學生個人獎懲補登","reward_group.php"=>"團體獎懲登記","reward_eli_new.php"=>"銷過","reward_stud_all.php"=>"個人獎懲明細","add_record.php"=>"學期獎懲補登","add_record_person.php"=>"個人學期獎懲補登","reward_list.php"=>"懲戒列表","reward_total.php"=>"學生獎懲統計","report.php"=>"列印獎懲公告","reward_exchange.php"=>"轉學生期中獎懲匯入記錄","rew_all.php"=>"獎懲總計");
	$student_menu_p2 = array("reward_eli.php"=>"未銷過學生","reward_eli2.php"=>"已銷過學生");
	
	$in_study="'0','15'";
?>
