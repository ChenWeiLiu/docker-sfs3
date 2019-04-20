<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

// 加入
$up_file_name =$upgrade_str."2005-10-21.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `school_board` ADD `poster_name` VARCHAR(20) NOT NULL default '';";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$query = "ALTER TABLE `school_board` ADD `poster_job` VARCHAR(20) NOT NULL default '';";
	if ($CONN->Execute($query))
		$temp_str .= "$query\n 更新成功 ! \n";
	else
		$temp_str .= "$query\n 更新失敗 ! \n";
	$temp_query = "新增發佈者資料欄位 -- by prolin (2005-10-21)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
