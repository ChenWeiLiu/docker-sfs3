<?php
// $Id: module-upgrade.php 7198 2013-03-06 07:09:51Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 將預設的成績單評語部份設為自動取得評語

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//刪除 seme_year_seme 誤值 991,992等三碼長度的資料
$up_file_name =$upgrade_str."2013-03-06.txt";

if (!is_file($up_file_name)){
	$query = "delete FROM `stud_seme_rew` WHERE length( seme_year_seme ) =3;";
	if ($CONN->Execute($query)) {
		$temp_query = "刪除 seme_year_seme 誤值 991,992等三碼長度的資料 -- by smallduh (2013-03-06)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}


//更新學習描述指令,讓系統可以自動取得各科描述資料
$up_file_name =$upgrade_str."2003-06-08.txt";

if (!is_file($up_file_name)){
	$query = "update score_input_col set interface_sn=1,col_text='學習描述文字說明',col_value='',col_type='text',col_fn='get_ss_score_memo',col_ss='y',col_comment='n',col_check='0',col_date=now() where col_sn = '27'";
	if ($CONN->Execute($query)) {
		$temp_query = "更新學習描述指令,讓系統可以自動取得各科描述資料 -- by hami (2003-06-08)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

// 建立和交換資料無關的成績選項
// 在 stud_seme_abs 加入一個 abs_kind 為 primary key 
$up_file_name =$upgrade_str."2003-06-22.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `stud_seme_abs` CHANGE `abs_kind` `abs_kind`TINYINT( 3 ) UNSIGNED NOT NULL ;";
	$query[1] = "ALTER TABLE `stud_seme_abs` DROP PRIMARY KEY,ADD PRIMARY KEY ( seme_year_seme, stud_id, `abs_kind` )";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修正學生學期出缺席總表的主要鍵 -- by hami (2003-06-22)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
	
}

// 舊格式轉換
$up_file_name =$upgrade_str."2003-06-20.txt";

if (!is_file($up_file_name)){
	$temp_str = "執行 up_acad.php";
	require "up_acad.php";
	$temp_query = "舊格式轉換 -- by hami (2003-06-20)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

//評語資料表的teacher_id型態修正
$up_file_name =$upgrade_str."2003-09-25.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `comment_kind` CHANGE `kind_teacher_id` `kind_teacher_id` VARCHAR( 20 ) DEFAULT NULL";
	$query[1] = "ALTER TABLE `comment_level` CHANGE `level_teacher_id` `level_teacher_id` VARCHAR( 20 ) DEFAULT NULL";
	$query[2] = "ALTER TABLE `comment` CHANGE `teacher_id` `teacher_id` VARCHAR( 20 ) DEFAULT NULL ";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "評語相關資料表comment,comment_kind,comment_level欄位型態更新 -- by jrh (2003-09-25)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}	
?>
