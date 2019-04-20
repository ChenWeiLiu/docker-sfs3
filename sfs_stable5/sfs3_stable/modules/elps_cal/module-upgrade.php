<?php
// $Id: module-upgrade.php 5619 2009-09-01 16:09:29Z infodaes $
if(!$CONN){echo "go away !!"; exit;}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2007-03-04.txt";

if (!is_file($up_file_name)){
	$query = "ALTER TABLE `cal_elps_set` ADD `week_mode` TINYINT( 1 ) DEFAULT '0' NOT NULL ";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$temp_query = "新增週次計算模式  by chi (2007-03-04)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2009-09-01.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `cal_elps` ADD `important` TINYINT DEFAULT '0' NOT NULL ;";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$temp_query = "新增學校大事記錄欄位(important)  by infodaes (2009-09-01)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
