<?php
// $Id: module-upgrade.php 7198 2013-03-06 07:09:51Z smallduh $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//修正 2018-01-22~2018-01-24 三天的資料, 屬於 106-2
$up_file_name =$upgrade_str."2018-03-03.txt";

if (!is_file($up_file_name)){
	$target_day=array("2018-01-22","2018-01-23","2018-01-24");
	foreach ($target_day as $fix_day) {
		$query = "select * FROM `stud_absent` WHERE date='$fix_day'";
		$res=$CONN->Execute($query);
		while ($row=$res->fetchRow()) {
			$class_id=$row['class_id'];
			$sasn=$row['sasn'];
			$c=explode("_",$class_id);
			$update_class_id=sprintf("%03d_%d_%02d_%02d",$c[0],2,$c[2],$c[3]);
			$update_query="update `stud_absent` set semester='2',class_id='$update_class_id' where sasn='$sasn'";
			$CONN->Execute($update_query);
		}
	}

		$temp_query = "修正 2018-01-22~2018-01-24 三天的資料, 屬於 106學年第2學期 -- by smallduh (2013-03-06)\n$query";
		$fp = fopen ($up_file_name, "w");
		fwrite($fp,$temp_query);
		fclose ($fd);
}

?>
