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
$up_file_name =$upgrade_str."2006-08-27.txt";
//新增 student_sn 欄位
if (!is_file($up_file_name)){
	$SQL = "ALTER TABLE `stud_sta` ADD `student_sn` INT( 10 ) UNSIGNED DEFAULT '0' NOT NULL AFTER `prove_id` ";
	$rs=$CONN->Execute($SQL);
	if ($rs) {$temp_query = "新增 student_sn 欄位資料表 stud_sta -- by chi (2006-08-27)\n $SQL";}
	else {$temp_query = "新增 student_sn 欄位資料表 stud_sta 失敗 !!,請手動更新下列語法\n $SQL";}
	$SQL = "select DISTINCT(a.stud_id),b.student_sn  from  `stud_sta` a, stud_base b  where a.stud_id=b.stud_id ";
	$rs=&$CONN->Execute($SQL) or die($SQL);
	$arr=&$rs->GetArray();//資料二維陣列
	$str='';
	foreach ($arr as $ary){
		$SQL="update `stud_sta` set student_sn='{$ary[student_sn]}' where  stud_id='{$ary[stud_id]}'  ";
		$rs=&$CONN->Execute($SQL) or die($SQL);
		if ($rs) $str.=$SQL."\n";
	}

	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query.$str);
	fclose ($fp);
	unset($temp_query);unset($str);
}
?>
