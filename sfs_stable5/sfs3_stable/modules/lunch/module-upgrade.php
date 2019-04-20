<?php
// $Id: module-upgrade.php 7055 2013-01-03 23:55:14Z hami $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");


//以上保留--------------------------------------------------------


//修改資料表，加入 pN 欄位，取消 pdate 索引值
$up_file_name =$upgrade_str."2004-06-14.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `lunchtb` DROP PRIMARY KEY" ;
	$query[1] = "ALTER TABLE `lunchtb` ADD `pN` INT( 11 )  NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST" ;
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改資料表 lunchtb，加入 pN 欄位，取消 pdate 索引值 -- by sula (2004-06-14)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}


//修改資料表，加入 pNutrition 欄位
$up_file_name =$upgrade_str."2010-05-16.txt";

if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `lunchtb` ADD `pNutrition` VARCHAR( 255 ) NULL;" ;
	$temp_str = '';
	$ii=count($query);
	for($i=0;$i<$ii;$i++) {
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "修改資料表 lunchtb，加入 pNutrition 欄位 -- by yjtzeng (2010-05-16)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);
}

?>
