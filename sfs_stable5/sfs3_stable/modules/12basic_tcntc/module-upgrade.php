<?php
// $Id:  $

if(!$CONN){
	echo "go away !!";
	exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2013-10-04.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `12basic_tcntc` CHANGE `disability_id` `disability_id` VARCHAR( 1 ) NULL DEFAULT NULL ;";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "身心障礙代碼欄位改為文字型態".$str." -- by infodaes (2013-10-04)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2014-02-27.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `12basic_tcntc` ADD `language_certified` TINYINT( 4 ) NULL DEFAULT 0 ;";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "增加族語認證註記欄位".$str." -- by infodaes (2014-02-27)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


?>
