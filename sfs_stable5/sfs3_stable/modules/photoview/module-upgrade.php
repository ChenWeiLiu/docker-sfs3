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
$up_file_name =$upgrade_str."2006-8-18.txt";
if (!is_file($up_file_name)){
	$CONN->Execute("alter table `photoviewtb` change `act_info` `act_info` TEXT not null default ''");
    $fp = fopen ($up_file_name, "w");
    $temp_query = "更改 act_info 屬性 -- by brucelyc (2006-8-18)";
    fwrite($fp,$temp_query);
	fclose($fd);
}
?>
