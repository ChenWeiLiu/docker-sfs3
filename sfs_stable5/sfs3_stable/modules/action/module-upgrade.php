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
$up_file_name =$upgrade_str."2008-05-17.txt";

//改變說明欄位為text型態
if (!is_file($up_file_name)){
	
	$SQL = "ALTER TABLE `actiontb` CHANGE `act_info` `act_info` TEXT NOT NULL ";
	$rs=$CONN->Execute($SQL);
	if ($rs) {$temp_query = "改變 actiontb  說明欄位為text型態 by hami (2007-03-21)\n $SQL";}
	else {$temp_query = "改變上傳檔案為text型態 actiontb  失敗 !!,請手動更新下列語法\n $SQL";}

	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query.$str);
	fclose ($fp);
	unset($temp_query);unset($str);
}

?>