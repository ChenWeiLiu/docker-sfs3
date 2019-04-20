<?php
//$Id: module-upgrade.php 6737 2012-04-06 12:25:56Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}
// reward_reason和reward_base 欄位屬性為text

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");


$up_file_name =$upgrade_str."2017-07-21.txt";
if (!is_file($up_file_name) and is_file("upgrade_sfscenter.php") and $SFS_IS_CENTER_VER==1){
        require ("upgrade_sfscenter.php");
        $temp_query = "中心端主機自動建立XML橋接主機授權 -- by smallduh (2017-07-21)\n\n$temp_str";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);
}


//以上保留--------------------------------------------------------
//更新記錄會開啟一個文字檔, 請以日期作為檔名, 以利辨別, 如: 2013-06-24.txt
/* 以下為範例
$up_file_name =$upgrade_str."2013-03-08.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `contest_record1` ADD `teacher_sn` int(10) NULL" ; //
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "查資料比賽增加記錄評分老師功能 -- by smallduh (2013-03-08)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}
*/



?>