<?php
// $Id: module-upgrade.php 8561 2015-10-14 02:36:14Z infodaes $

if(!$CONN){
	echo "go away !!";
	exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");


$up_file_name =$upgrade_str."2008-11-02.txt";
if (!is_file($up_file_name)){
	$query ="CREATE TABLE `stud_kind_group` (
  `sn` int(11) NOT NULL auto_increment,
  `description` varchar(40) NOT NULL,
  `kind_list` varchar(100) NOT NULL,
  PRIMARY KEY  (`sn`),
  KEY `description` (`description`)
);";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "增加stud_kind_group資料表".$str." -- by infodaes (2008-11-02)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


$up_file_name =$upgrade_str."2015-10-14.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_subkind` ADD `ext1` VARCHAR(20) NULL AFTER `note`, ADD `ext2` VARCHAR(20) NULL AFTER `ext1`;";
	$CONN->Execute($query);
	$query ="ALTER TABLE `stud_subkind_ref` ADD `ext1_title` VARCHAR(20) NULL , ADD `ext2_title` VARCHAR(20) NULL , ADD `ext1` TEXT NULL , ADD `ext2` TEXT NULL ;";
	if($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "增加ext1 ext2 欄位".$str." -- by infodaes (2015-10-14)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
