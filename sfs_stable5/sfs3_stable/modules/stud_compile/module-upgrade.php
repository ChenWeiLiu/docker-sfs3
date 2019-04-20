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
$up_file_name =$upgrade_str."2004-7-13.txt";
if (!is_file($up_file_name) ){
	$query = "select bs from stud_compile";
	if (!$CONN->Execute($query)){
		$query = "alter table stud_compile add bs varchar(11)";
		if($CONN->Execute($query)){
			$temp_query = "已加入 bs 欄位 -- by hami (2004-07-13)\n$query";
		}
	}
	else {
			$temp_query = "已加入 bs 欄位 -- by hami (2004-07-13)\n$query";
	}
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
	
}

?>
