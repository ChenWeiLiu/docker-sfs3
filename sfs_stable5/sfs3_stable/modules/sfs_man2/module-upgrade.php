<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查分類與群組重複 

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-10-16.txt";

if (!is_file($up_file_name)){
	$query = " select msn from sfs_module where msn=of_group and  kind='分類'";
	$res = $CONN->Execute($query);
	while(!$res->EOF){
		$msn = $res->fields[msn];
		if ($msn==16 or $msn==17) //教學組 註冊組 歸到教務
			$CONN->Execute("update sfs_module set of_group=12 where msn=$msn");
		else
			$CONN->Execute("update sfs_module set of_group=0 where msn=$msn");
		$res->MoveNext();
	}
	
	$temp_query = "分類程式錯誤修正 -- by hami (2003-10-15)\n";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

/**
* 更改 變數說明欄位值
*/

$up_file_name =$upgrade_str."2006-2-10.txt";

if (!is_file($up_file_name)){
	$query = "ALTER TABLE `pro_module` CHANGE `pm_memo` `pm_memo` VARCHAR( 200 ) NOT NULL ";
	$res = $CONN->Execute($query);
	
	$temp_query = "更改 變數說明欄位值-- by hami (2006-2-10)\n";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}


?>
