<?php
// $Id: module-upgrade.php 9240 2018-05-04 03:25:04Z igogo $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2009-10-27.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists score_manage_out (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		reason text NOT NULL default '',
		PRIMARY KEY (year,semester,student_sn)
		)";
	if ($CONN->Execute($creat_table_sql)) {
		$temp_query = "建立成績除外學生表 -- by brucelyc (2009-10-27)";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}
?>
