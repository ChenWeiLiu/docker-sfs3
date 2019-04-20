<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path();
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2004-05-27.txt";

if (!is_file($up_file_name)){
	$query = "ALTER TABLE `elective_tea` ADD `course_id` mediumint(8) unsigned NOT NULL default '0';";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$temp_query = "新增取代課程代碼欄位 -- by brucelyc (2004-05-27)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
