<?php
// $Id: module-upgrade.php 6065 2010-08-31 13:09:26Z infodaes $

if(!$CONN){
	echo "go away !!";
	exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2007-03-18.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `charge_item` ADD `cooperate` TINYINT(4)";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "於 charge_item 資料表補入 cooperate 欄位".$str." -- by brucelyc (2007-03-18)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2010-02-13.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `charge_decrease` CHANGE `percent` `percent` FLOAT DEFAULT '0'";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "改變charge_decrease減免記錄欄位percvent為浮點數".$str." -- by infodaes(2010-02-13)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2010-08-27.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `charge_detail` ADD `detail_type` TINYINT UNSIGNED DEFAULT '0' NOT NULL AFTER `item_id`;";
	if ($CONN->Execute($query)){
		$str="成功\ ";
		$CONN->Execute("UPDATE `charge_detail` SET `detail_type`='0' WHERE `detail_type`=NULL;");
		}
	else $str="失敗";
	$temp_query = "增加細目收歸帳戶欄位，以利中商銀CSV匯出".$str." -- by infodaes(2010-08-27)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
