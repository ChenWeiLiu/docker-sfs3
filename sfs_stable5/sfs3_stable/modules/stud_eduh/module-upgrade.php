<?php

//$Id: module-upgrade.php 8490 2015-08-18 03:23:08Z smallduh $

if(!$CONN){
	echo "go away !!";
	exit;
}

//更動 欄位屬性
//加入 teacher_sn
// 檢查更新否
// 更新記錄檔路徑
//$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_path = "upgrade/modules/stud_eduh/";
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2003-06-01.txt";
//echo get_store_path();
if (!is_file($up_file_name)){

	//SQL 語法
	$query = "ALTER TABLE `stud_seme_test` ADD `teacher_sn` INT UNSIGNED NOT NULL";
	// 資料表名稱
	$arr[0][table_name]='stud_seme_test';

	// 檢查欄位
	$arr[0][field_name]='teacher_sn';

	// 該欄位不存在資料表中
	$arr[0][check_in_table] = 0;

	// 更改 stud_seme_test 表的 st_id 屬性
	if (upgrade_table ($query,$arr))
		//更改 st_id 屬性為 AUTO_INCREMENT
		upgrade_table("ALTER TABLE `stud_seme_test` CHANGE `st_id` `st_id` BIGINT( 20 ) UNSIGNED DEFAULT '0' NOT NULL AUTO_INCREMENT");

		$temp_query = "更動 欄位屬性加入 teacher_sn -- by hami (2003-06-01)";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);

}

//新增符合 XML 3.0格式的心理測驗資料表
$up_file_name =$upgrade_str."2007-01-07.txt";
if (!is_file($up_file_name)){

	//SQL 語法
	$query = "CREATE TABLE `stud_psy_test` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `year` tinyint(4) unsigned NOT NULL default '0',
  `semester` tinyint(4) unsigned NOT NULL default '0',
  `student_sn` int(10) unsigned NOT NULL default '0',
  `item` varchar(60) NOT NULL default '',
  `score` varchar(10) default NULL,
  `model` varchar(30) NOT NULL default '',
  `standard` varchar(10) NOT NULL default '',
  `pr` varchar(10) NOT NULL default '',
  `explanation` varchar(60) NOT NULL default '',
  `teacher_sn` int(10) unsigned NOT NULL default '0',
  `update_time` datetime default NULL,
  PRIMARY KEY  (`sn`)
);";
  if ($CONN->Execute($query)) 
		$temp_query = "新增符合 XML 3.0格式的心理測驗資料表 -- by infodaes (2007-01-07)\n$query";
	else
		$temp_query = "新增符合 XML 3.0格式的心理測驗資料表 stud_psy_tes 失敗 !!,請手動更新下列語法\n $query";

        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);
}

//XML 3.0格式的心理測驗資料表 -- 加註　測驗日期
$up_file_name =$upgrade_str."2007-01-10.txt";
if (!is_file($up_file_name)){

	//SQL 語法
	$query = "ALTER TABLE `stud_psy_test` ADD `test_date` DATE AFTER `semester`";

  if ($CONN->Execute($query)) 
		$temp_query = "新增XML 3.0格式的心理測驗資料表 -- 測驗日期 欄位-- by infodaes (2007-01-07)\n$query";
	else
		$temp_query = "新增XML 3.0格式的心理測驗資料表 -- 測驗日期 欄位 失敗 !!,請手動更新下列語法\n $query";

        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);
}

//增加3.0格式的心理測驗資料表的欄位寬度
$up_file_name =$upgrade_str."2008-01-10.txt";
if (!is_file($up_file_name)){

	//SQL 語法
	$query = "ALTER TABLE `stud_psy_test` CHANGE `item` `item` VARCHAR( 60 ) ,
CHANGE `score` `score` VARCHAR( 40 ) DEFAULT NULL ,
CHANGE `model` `model` VARCHAR( 40 ) ,
CHANGE `standard` `standard` VARCHAR( 40 ) ,
CHANGE `pr` `pr` VARCHAR( 40 ) ,
CHANGE `explanation` `explanation` VARCHAR( 100 )";

  if ($CONN->Execute($query)) 
		$temp_query = "更改紀錄欄位寬度-- by infodaes (2008-01-10)\n$query";
	else
		$temp_query = "更改紀錄欄位寬度失敗 !!,請手動更新下列語法\n $query";

        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fd);
}


$up_file_name =$upgrade_str."2014-09-02.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_seme_talk` ADD `interview_method` varchar(10) AFTER `interview`;";
	if ($CONN->Execute($query))
		$str="新增訪談方式欄位成功\";
	else
		$str="新增訪談方式欄位失敗";
	$temp_query = "於stud_seme_talk 資料表新增訪談方式 -- by infodaes 2014-09-02 \n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2015-08-18.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `stud_base` CHANGE `stud_sex` `stud_sex` TINYINT( 3 ) NULL DEFAULT NULL;";
	if ($CONN->Execute($query))
		$str="修正性別欄位設定成功\";
	else
		$str="修正性別欄位設定失敗";
	$temp_query = "取消 `stud_base` 資料表 stud_sex 欄位的 unsign 設定 -- by smallduh 2015-08-18 \n$query";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

 

?>
