<?php
// $Id: module-upgrade.php 5310 2009-01-10 07:57:56Z hami $


// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");


//以上保留--------------------------------------------------------


//修改資料表，加入 school_list 欄位

$up_file_name =$upgrade_str."2004-10-22.txt";
if (!is_file($up_file_name)){
	$query=" ALTER TABLE sign_act_kind ADD school_list  TEXT NOT NULL  ; ";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 校際報名新增 school_list 欄建立成功 ! \n";
	else
		$temp_str = "$query\n 校際報名新增 school_list 欄建立失敗 ! \n";

	$temp_query = "校際報名新增欄位新增 -- by prolin (2004-10-22)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
