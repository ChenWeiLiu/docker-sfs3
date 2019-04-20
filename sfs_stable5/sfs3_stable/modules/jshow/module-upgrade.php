<?php

// $Id: module-upgrade.php 7779 2013-11-20 16:09:00Z smallduh $
if (!$CONN) {
    echo "go away !!";
    exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/" . get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name = $upgrade_str . "2014-05-22.txt";
if (!is_file($up_file_name)) {
	//增加
	$query = "ALTER TABLE `jshow_pic` ADD url_click int(6) NOT NULL";
	$CONN->Execute($query);
	$fp = fopen($up_file_name, "w");
	$temp_query = "加入連結次數記錄欄位	-- by smallduh (2014-05-22)";
	fwrite($fp, $temp_query);
	fclose($fp);
}

$up_file_name = $upgrade_str . "2014-05-19.txt";
if (!is_file($up_file_name)) {
	//增加
	$query = "ALTER TABLE `jshow_pic` ADD url text NOT NULL";
	$CONN->Execute($query);
	$fp = fopen($up_file_name, "w");
	$temp_query = "加入「超連結」欄位	-- by smallduh (2014-05-19)";
	fwrite($fp, $temp_query);
	fclose($fp);
}

/* 預留範例
$up_file_name = $upgrade_str . "2014-03-20.txt";
if (!is_file($up_file_name)) {
	//增加
	$query = "ALTER TABLE `jboard_kind` ADD position tinyint(1) NOT NULL";
	$CONN->Execute($query);
	$fp = fopen($up_file_name, "w");
	$temp_query = "分類列表加入「層級」欄位	-- by smallduh (2013-12-17)";
	fwrite($fp, $temp_query);
	fclose($fp);
}
*/

