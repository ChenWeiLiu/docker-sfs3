<?php

// $Id: stud_move_config.php 9114 2017-08-04 15:18:55Z smallduh $

	//系統設定檔
	include "../../include/config.php";
	//函式庫
	include "../../include/sfs_case_PLlib.php";    
	//檢查更新指令
	include_once "module-upgrade.php";
	//新增按鈕名稱
	$newBtn = " 新增資料 ";
	//修改按鈕名稱
	$editBtn = " 確定修改 ";
	//刪除按鈕名稱
	$delBtn = " 確定刪除 ";
	//確定新增按鈕名稱
	$postMoveBtn = " 確定異動 ";
	$postInBtn = " 確定轉入 ";
	$postOutBtn = " 確定轉出 ";
	$postReinBtn = " 確定復學 ";
	$postHome = " 確定在家自學 ";
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
	//調出類別
	$out_arr=array("7"=>"出國","8"=>"調校","11"=>"死亡","12"=>"中輟");
	//復學選擇類別
	$out_in_arr=array("7"=>"出國","8"=>"調校","12"=>"中輟");
	//升降級
	$demote_arr=array("9"=>"升級","10"=>"降級");
	//復學類別
	$rein_arr=array("3"=>"中輟復學","14"=>"轉學復學");
	//目錄內程式
	$student_menu_p = array("stud_move.php"=>"轉入","stud_move_out.php"=>"調出","stud_move_rein.php"=>"復學","stud_move_home.php"=>"在家自學","stud_demote.php"=>"升降級","stud_move_gradu.php"=>"畢業轉出","stud_move_new.php"=>"新生入學","stud_move_list2.php"=>"異動記錄列表","stud_move_print.php"=>"異動報表","stud_move_cal.php"=>"異動統計表","../stud_reg/"=>"學籍管理","stud_move_chiedit.php"=>"編修作業");

//big5轉 utf8
function big5_to_utf8($str){
	$str = mb_convert_encoding($str, "UTF-8", "BIG5");

	$i=1;

	while ($i != 0){
		$pattern = '/&#\d+\;/';
		preg_match($pattern, $str, $matches);
		$i = sizeof($matches);
		if ($i !=0){
			$unicode_char = mb_convert_encoding($matches[0], 'UTF-8', 'HTML-ENTITIES');
			$str = preg_replace("/$matches[0]/",$unicode_char,$str);
		} //end if
	} //end wile

	return $str;

}

//base64解碼
function array_base64_decode($data) {
	foreach($data as $key=>$value){
		if (is_array($value)){
			$data[$key] = array_base64_decode($value);
		}else{
			$data[$key]= base64_decode($value);
		}
	} // end foreach

	return $data;
}

//檢查 table 欄位, 有些學校因資料庫升級失敗, 欄位缺失
function check_table_column() {
   global $CONN;

	//2017-07-14 下載期限
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_deadline'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_deadline` DATE NOT NULL DEFAULT '0000-00-00'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_deadline'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_deadline` DATE NOT NULL DEFAULT '0000-00-00'");
	}

	//2017-07-18 下載次數
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_limit'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_limit` INT NOT NULL COMMENT '下載次數限制'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_limit'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_limit` INT NOT NULL COMMENT '下載次數限制'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move` LIKE 'download_times'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move` ADD `download_times` INT NOT NULL COMMENT '下載次數'");
	}
	$res=$CONN->Execute("SHOW COLUMNS FROM `stud_move_import` LIKE 'download_times'");
	if ($res->recordCount()==0) {
		$CONN->Execute("ALTER TABLE `stud_move_import` ADD `download_times` INT NOT NULL COMMENT '下載次數';");
	}
}

?>
