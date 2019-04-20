<?php
// $Id: module-upgrade.php 5419 2009-03-06 02:38:26Z brucelyc $
if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2013-04-18.txt";
//改變上傳檔案為text型態以便可以多檔附件
if (!is_file($up_file_name)){
	$SQL = "ALTER TABLE career_self_ponder DROP INDEX `student_sn`,ADD UNIQUE `student_sn` (`student_sn` ,`id`)";
	$rs=$CONN->Execute($SQL);
	if ($rs) {$temp_query = "更改索引型態成功-- by infodaes (2013-04-18)\n $SQL";}
	else {$temp_query = "更改索引型態失敗 !!,請手動更新下列語法\n $SQL";}

	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query.$str);
	fclose ($fp);
	unset($temp_query);unset($str);
}

$up_file_name =$upgrade_str."2013-08-02.txt";
if (!is_file($up_file_name)){
	$SQL = "ALTER TABLE `career_race` ADD `race_bonus` FLOAT NOT NULL DEFAULT '1';";
	$rs=$CONN->Execute($SQL);
	if ($rs) {$temp_query = "增加 競賽積分權重欄位 成功-- by infodaes (2013-04-18)\n $SQL";}
	else {$temp_query = "增加 競賽積分權重欄位 失敗 !,請手動更新下列語法\n $SQL";}

	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query.$str);
	fclose ($fp);
	unset($temp_query);unset($str);
}