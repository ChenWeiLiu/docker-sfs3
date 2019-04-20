<?php

//$Id: module-upgrade.php 8094 2014-08-24 16:25:12Z infodaes $

if(!$CONN){
	echo "go away !!";
	exit;
}

//更動 欄位屬性
//加入 teacher_sn
//
// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-06-01.txt";
//echo get_store_path();
if (!is_file($up_file_name)){
	//SQL 語法
	$query = "ALTER TABLE `stud_seme_test` ADD `teacher_sn` INT UNSIGNED NOT NULL";
	// 資料表名稱
	$arr[0][table_name]='stud_seme_test';

	// 檢查欄位
	$arr[0][field_name]='teacher_sn';

	// 該欄位不存在資料表中
	$arr[0][check_in_table] = 0;

	// 更改 stud_seme_test 表的 st_id 屬性
	if (upgrade_table ($query,$arr))
		//更改 st_id 屬性為 AUTO_INCREMENT
		upgrade_table("ALTER TABLE `stud_seme_test` CHANGE `st_id` `st_id` BIGINT( 20 ) UNSIGNED DEFAULT '0' NOT NULL AUTO_INCREMENT");

		$temp_query = "更動 欄位屬性加入 teacher_sn -- by hami (2003-06-01)";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);

}

$up_file_name =$upgrade_str."2004-11-29.txt";

if (!is_file($up_file_name)){
	$query="select * from stud_ext_data_menu where 1=0";
	if ($CONN->Execute($query))
		$temp_str = "學生補充資料選項表已存在, 無需升級。";
	else {
			
		$query=" CREATE TABLE if not exists  `stud_ext_data_menu` (
			`id` INT NOT NULL AUTO_INCREMENT ,
			`ext_data_name` VARCHAR( 50 ) NOT NULL ,
			`doc` TEXT,
			PRIMARY KEY ( `id` )
			) COMMENT = '學生補充資料選項' " ;	
		if ($CONN->Execute($query))
			$temp_str = "$query\n 學生補充資料表建立成功 ! \n";
		else
			$temp_str = "$query\n 學生補充資料表建立失敗 ! \n";

	}
	$query="select * from stud_ext_data where 1=0";
	if ($CONN->Execute($query))
		$temp_str = "學生補充資料選項表已存在, 無需升級。";
	else {
		
		$query=" CREATE TABLE if not exists stud_ext_data (
  			stud_id varchar(8) NOT NULL default '',
  			mid int(11) NOT NULL default '0',
  			ext_data text NOT NULL,
  			teach_id varchar(10) NOT NULL default '',
  			ed_date date NOT NULL default '0000-00-00',
  			update_time timestamp(14) NOT NULL,
  			PRIMARY KEY  (stud_id,mid)
			);" ;	
			
		if ($CONN->Execute($query))
			$temp_str = "$query\n 學生補充個人資料表建立成功 ! \n";
		else
			$temp_str = "$query\n 學生補充個人資料表建立失敗 ! \n";

	}
	$temp_query = "學生補充資料表格建立 -- by prolin (2004-9-25)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2009-02-02.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_domicile` ADD `fath_grad_kind` TINYINT( 4 ) DEFAULT '1' AFTER `fath_education` , ADD `moth_grad_kind` TINYINT( 4 ) DEFAULT '1' AFTER `moth_education` ;";
	if ($CONN->Execute($query))
		$str="新增畢修業別欄位成功\";
	else
		$str="新增畢修業別欄位失敗";
	$temp_query = "於 stud_domicile 資料表新增父母畢修業別欄位以符合XML 3.0".$str." -- by infodaes 2009-02-02 \n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


$up_file_name =$upgrade_str."2014-08-24.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_seme_talk` ADD `interview_method` varchar(10) AFTER `interview`;";
	if ($CONN->Execute($query))
		$str="新增訪談方式欄位成功\";
	else
		$str="新增訪談方式欄位失敗";
	$temp_query = "於stud_seme_talk 資料表新增訪談方式 -- by infodaes 2014-08-24 \n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
