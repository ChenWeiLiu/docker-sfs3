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
$up_file_name =$upgrade_str."2003-06-09.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `teacher_post` DROP PRIMARY KEY ;";

	if ($CONN->Execute($query)) {
		$query2 =" ALTER TABLE `teacher_post` ADD UNIQUE (`teacher_sn`);";
		$CONN->Execute($query2);
		$temp_query = "更新teacher_post  primary key 值 -- by hami (2003-06-09)\n$query2";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

?>
