<?php
//$Id: module-upgrade.php 6737 2012-04-06 12:25:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason和reward_base 欄位屬性為text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//以上保留--------------------------------------------------------
//更新記錄會開啟一個文字檔, 請以日期作為檔名, 以利辨別, 如: 2013-06-24.txt
/* 以下為範例
$up_file_name =$upgrade_str."2013-03-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_record1` ADD `teacher_sn` int(10) NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "查資料比賽增加記錄評分老師功能 -- by smallduh (2013-03-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}
*/

$up_file_name =$upgrade_str."2015-03-10.txt";
if (!is_file($up_file_name)){
	$query = array();
	//在 resit_paper_setup 裡增加一個出題模式欄位 0表亂數出題 , 1表依不及格分科
	$query[0] = "ALTER TABLE `resit_paper_setup` ADD `item_mode` tinyint(1) not NULL" ; 
	//在 resit_exam_items 裡增加一個欄位記錄試題屬於那一個分科 
	$query[1] = "ALTER TABLE `resit_exam_items` ADD `subject` varchar(30) not NULL" ; 
	//在 resit_exam_score 裡增加一個欄位記錄學生不及格分科 
	$query[2] = "ALTER TABLE `resit_exam_score` ADD `subjects` varchar(50) not NULL" ; 

	//新增資料表 resit_scope_subject 每學期某年級某領域包含的分科題數
	$query[3] = "
	CREATE TABLE IF NOT EXISTS `resit_scope_subject` (
   `sn` int(10) unsigned NOT NULL auto_increment,
   `seme_year_seme` varchar(4) NOT NULL,
   `cyear` tinyint(1) not null,
   `scope` varchar(30) not null,
   `subject_id` int(3) not null,
	 `subject` varchar(50) not null,
	 `items` int(3) not null,
   PRIMARY KEY  (`sn`)
	) ENGINE=MyISAM;	 
	";
	
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "增加各領域依分科命題成卷功能 -- by smallduh (2015-03-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2015-08-06.txt";
if (!is_file($up_file_name)){
	$query = array();
	//在 resit_paper_setup 裡增加一個出題模式欄位 0表亂數出題 , 1表依不及格分科
	$query[0] = "ALTER TABLE `resit_paper_setup` ADD `top_marks` int(3) not NULL" ; 
	
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "增加試卷總分設定 -- by smallduh (2015-08-06)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2017-01-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	//更改欄位資料格式為 longtext
	$query[0] = "ALTER TABLE `resit_exam_items` CHANGE `question` `question` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
	$query[1] = "ALTER TABLE `resit_exam_items` CHANGE `cha` `cha` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
	$query[2] = "ALTER TABLE `resit_exam_items` CHANGE `chb` `chb` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
	$query[3] = "ALTER TABLE `resit_exam_items` CHANGE `chc` `chc` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";
	$query[4] = "ALTER TABLE `resit_exam_items` CHANGE `chd` `chd` LONGTEXT CHARACTER SET latin1 COLLATE latin1_swedish_ci NOT NULL";

	$temp_str = '';
	for($i=0;$i<count($query);$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改欄位 question , cha, chb, chc, chd 的格式為 longtext -- by smallduh (2017-01-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


?>