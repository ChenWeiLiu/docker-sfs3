<?php
if(!$CONN){
        echo "go away !!";
        exit;
}

$upgrade_path = "upgrade/".get_store_path();
$upgrade_str = set_upload_path("$upgrade_path");

//以上保留--------------------------------------------------------

//修改資料表，增加承辦單位
$up_file_name =$upgrade_str."2014-09-01.txt";

if (!is_file($up_file_name)){
	
	$query = "ALTER TABLE teacher_absent add note_file varchar(100) " ; //上傳文件檔名
	$temp_str = '';
		if ($CONN->Execute($query))
			$temp_str .= "$query\n 更新成功 ! \n";
		else
			$temp_str .= "$query\n 更新失敗 ! \n";
	

	$temp_query = "修改資料表 teacher_absent (教師請假記錄)-- by hami (2014-09-01)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fd);

}

?>