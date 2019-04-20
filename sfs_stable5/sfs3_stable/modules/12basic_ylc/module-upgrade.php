<?php
//$Id: $

if(!$CONN){
	echo "go away !!";
	exit;
}

//更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//增加身心障礙欄位
$up_file_name = $upgrade_str."2013-09-30.txt";
if (!is_file($up_file_name)) {
	$temp_str = $query." 增加身心障礙欄位更新失敗 -- by 雲林縣學務系統小組 (2013-10-01) !\n";
	$query = "ALTER TABLE `12basic_ylc` ADD `disability_id` tinyint(3) unsigned DEFAULT NULL";
	if ($CONN->Execute($query)) {
		$query = "ALTER TABLE `12basic_kind_ylc` ADD `disability_data` text NOT NULL";
		if ($CONN->Execute($query)) {
			$temp_str = $query." 增加身心障礙欄位 disability_id 與 disability_data 資料成功 -- by 雲林縣學務系統小組 (2013-09-30) !\n";
		}
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp, $temp_str);
	fclose ($fp);
}

//修改身心障礙欄位型態ALTER TABLE  `12basic_ylc` CHANGE  `disability_id`  `disability_id` VARCHAR( 3 ) NOT NULL COMMENT  '身心障礙'
$up_file_name = $upgrade_str."2014-02-27.txt";
if (!is_file($up_file_name)) {
	$temp_str = $query." 修改身心障礙欄位型態失敗 -- by 雲林縣學務系統小組 (2014-02-27) !\n";
	$query = "ALTER TABLE `12basic_ylc` CHANGE `disability_id` `disability_id` VARCHAR(3) NOT NULL";
	if ($CONN->Execute($query)) {
		$temp_str = $query." 修改身心障礙欄位型態 disability_id 成功 -- by 雲林縣學務系統小組 (2014-02-27) !\n";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp, $temp_str);
	fclose ($fp);
}

//增加資料封存欄位
$up_file_name = $upgrade_str."2017-11-20.txt";
if (!is_file($up_file_name)) {
	$temp_str = $query." 增加資料封存欄位更新失敗 -- by 雲林縣學務系統小組 (2017-11-20) !\n";
	$query = "ALTER TABLE `12basic_ylc` ADD `editable` tinyint(3) unsigned DEFAULT 1";
	if ($CONN->Execute($query)) {
		$temp_str = $query." 增加資料封存欄位 editable 資料成功 -- by 雲林縣學務系統小組 (2017-11-20) !\n";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp, $temp_str);
	fclose ($fp);
}

?>