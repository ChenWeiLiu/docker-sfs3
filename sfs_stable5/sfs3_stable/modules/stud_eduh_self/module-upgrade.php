<?php

//$Id:  $

if(!$CONN){
	echo "go away !!";
	exit;
}

//加入 highest 欄位

$upgrade_path = "upgrade/modules/stud_eduh/";
$upgrade_str = set_upload_path("$upgrade_path");

//新增 highest 欄位以紀錄最高分項測驗項目
$up_file_name =$upgrade_str."2013-03-26.txt";
if (!is_file($up_file_name)){
	//SQL 語法
	$query = "ALTER TABLE `career_test` ADD `highest` VARCHAR(100) NULL AFTER `content`;";
	if ($CONN->Execute($query)) 
		$temp_query = "新增 highest 欄位以紀錄最高分項測驗項目 成功！ -- by infodaes (2013-03-26)\n$query";
	else
		$temp_query = "新增 highest 欄位以紀錄最高分項測驗項目 失敗！ 請手動更新下列語法\n $query";

        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);
}


?>
