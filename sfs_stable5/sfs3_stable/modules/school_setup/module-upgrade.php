<?php
// $Id: module-upgrade.php 7453 2013-08-30 00:56:09Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path();
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2005-04-06.txt";

if (!is_file($up_file_name)){
	$query = "ALTER TABLE `school_base` change `sch_ename` `sch_ename` varchar(60) NOT NULL default ''";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$temp_query = "更改學校英文名長度 -- by brucelyc (2005-04-06)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$upgrade_path = "upgrade/".get_store_path();
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2013-08-30.txt";

if (!is_file($up_file_name)){
	// 先將 teach_title_id =0 刪除，以免更新失敗
	$query = "DELETE FROM  `teacher_title` WHERE teach_title_id=0";
	$CONN->Execute($query);
	$query = "ALTER TABLE `teacher_title` CHANGE `teach_title_id` `teach_title_id` int UNSIGNED NOT NULL AUTO_INCREMENT";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 更新成功 ! \n";
	else
		$temp_str = "$query\n 更新失敗 ! \n";
	$temp_query = "更改 teacher_title_id 為 AUTO_INCREMENT -- by hami (2013-08-29)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
