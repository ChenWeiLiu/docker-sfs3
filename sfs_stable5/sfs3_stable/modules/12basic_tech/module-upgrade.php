<?php
// $Id:  $

if(!$CONN){
	echo "go away !!";
	exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2014-04-01.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `12basic_tech` ADD `signup_north` varchar( 3 ) NULL DEFAULT '000',ADD `signup_central` varchar( 3 ) NULL DEFAULT '000',ADD `signup_south` varchar( 3 ) NULL DEFAULT '000',ADD `signup_memo` TEXT NULL ;";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "增加報名學校註記欄位".$str." -- by infodaes (2014-04-01)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//增加教育會考准考證號碼
$up_file_name =$upgrade_str."2015-05-05.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `12basic_tech` ADD `acad_exam_reg_num` varchar( 10 ) NULL DEFAULT '';";
	if ($CONN->Execute($query))
		$str="成功\ ";
	else
		$str="失敗";
	$temp_query = "增加會考准考證號欄位".$str." -- by smallduh (2015-05-05)\n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
