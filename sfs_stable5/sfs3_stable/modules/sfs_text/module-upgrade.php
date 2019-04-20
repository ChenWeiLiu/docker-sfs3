<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 將預設的成績單評語部份設為自動取得評語

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-06-17.txt";

if (!is_file($up_file_name)){
	$query = "delete from sfs_text where t_kind='course9' or t_kind='subject_kind'";
	$CONN->Execute($query);
	$query2 = "update sfs_text set g_id=4 where t_kind='non_display'";
	if ($CONN->Execute($query2)) {
		$temp_query = "刪除學習領域及科目名稱(已在新成績系統中設定),更改不顯示目錄到程式模組選項 -- by hami (2003-06-17)\n$query \n$query2";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
	}
}

?>
