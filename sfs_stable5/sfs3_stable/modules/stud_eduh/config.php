<?php

// $Id: config.php 9162 2017-10-25 06:39:45Z infodaes $

require_once "./module-cfg.php";

include_once "../../include/config.php";
include_once "../../include/sfs_case_PLlib.php";
//升級檢查 
require "module-upgrade.php";

//取得模組設定
$m_arr = get_sfs_module_set("stud_eduh");
extract($m_arr, EXTR_OVERWRITE);


//預設第一個開啟年級
$default_begin_class = 6;
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
//$menu_p = array("stud_eduh_personal.php"=>"個人輔導記錄列表","stud_eduh_list.php"=>"學生輔導記錄","stud_seme_talk2.php"=>"學生訪談記錄","stud_seme_spe2.php"=>"特殊表現記錄","stud_seme_test.php"=>"心理測驗記錄","stud_psy_tran.php"=>">>轉表>>","stud_psy_test.php"=>"心理測驗記錄(XML3.0新)","index2.php"=>"輔導記錄表","stud_eduh_report.php"=>"家庭狀況統計");
$menu_p = array(
"stud_eduh_personal.php"=>"個人輔導記錄列表",
"stud_eduh_list.php"=>"學生輔導記錄","stud_seme_talk2.php"=>"學生訪談記錄",
"chc_talk_all.php"=>"訪談記錄(全)","stud_seme_spe2.php"=>"特殊表現記錄",
"stud_psy_tran.php"=>"XML3.0轉表","stud_psy_test.php"=>"心理測驗記錄",
"index2.php"=>"輔導記錄表",
"index2_add.php"=>"輔導訪談記錄貼條 ",
"stud_psy_test_list.php"=>"心理測驗記錄貼條",
"index2_html.php"=>"網頁式輔導記錄表",
"stud_eduh_report.php"=>"家庭狀況統計",
"diff.php"=>"親子年齡差距45歲以上"
);

