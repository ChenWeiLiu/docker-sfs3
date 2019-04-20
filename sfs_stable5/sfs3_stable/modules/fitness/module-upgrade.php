<?php
// $Id: module-upgrade.php 8403 2015-04-30 14:29:08Z infodaes $
if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2013-12-24.txt";

if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$MODULE_SQL_FILE=dirname(__FILE__)."/module-new.sql";
	$query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));
	run_sql($query, $mysql_db);
	$query="select * from fitness_mod where 1=0 limit 0,1";
	if ($CONN->Execute($query))
		$temp_str = "更新成功!\n";
	else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "更新體適能常模 -- by smallduh (2013-12-24)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2013-12-24.txt";
if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$MODULE_SQL_FILE=dirname(__FILE__)."/module-new.sql";
	$query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));
	run_sql($query, $mysql_db);
	$query="select * from fitness_mod where 1=0 limit 0,1";
	if ($CONN->Execute($query))
		$temp_str = "更新成功!\n";
	else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "更新體適能常模 -- by smallduh (2013-12-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


$up_file_name =$upgrade_str."2012-12-26.txt";
if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$MODULE_SQL_FILE=dirname(__FILE__)."/module-new.sql";
	$query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));
	run_sql($query, $mysql_db);
	$query="select * from fitness_mod where 1=0 limit 0,1";
	if ($CONN->Execute($query))
		$temp_str = "更新成功!\n";
	else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "依教育部體適能網站,更新體適能常模 -- by smallduh (2012-12-26)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2012-10-26.txt";
if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$MODULE_SQL_FILE=dirname(__FILE__)."/module-new.sql";
	$query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));
	run_sql($query, $mysql_db);
	$query="select * from fitness_mod where 1=0 limit 0,1";
	if ($CONN->Execute($query))
		$temp_str = "更新成功!\n";
	else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "更新體適能常模 -- by smallduh (2012-10-26)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
$up_file_name =$upgrade_str."2006-12-29.txt";
if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$MODULE_SQL_FILE=dirname(__FILE__)."/module-new.sql";
	$query = fread(fopen($MODULE_SQL_FILE, 'r'), filesize($MODULE_SQL_FILE));
	run_sql($query, $mysql_db);
	$query="select * from fitness_mod where 1=0 limit 0,1";
	if ($CONN->Execute($query))
		$temp_str = "更新成功!\n";
	else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "更新體適能常模 -- by brucelyc (2006-12-29)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2006-12-30.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `fitness_data` ADD `student_sn` INT(10) DEFAULT '0'";
	if ($CONN->Execute($query)) {
		$temp_str = "更新成功!\n";
		$query="select student_sn,stud_id from stud_base";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			$CONN->Execute("update fitness_data set student_sn='".$res->fields[student_sn]."' where stud_id='".$res->fields[stud_id]."'");
			$res->MoveNext();
		}
		$CONN->Execute("ALTER TABLE `fitness_data` DROP `stud_id`");
		$CONN->Execute("ALTER TABLE `fitness_data` DROP PRIMARY KEY, ADD PRIMARY KEY (c_curr_seme,student_sn)");
	} else
		$temp_str = "更新失敗 ! \n";
	$temp_query = "加入student_sn欄位 -- by brucelyc (2006-12-30)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2013-04-01.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `fitness_data` ADD `organization` VARCHAR(100) NULL";
	if ($CONN->Execute($query)) {
		$temp_str = "更新成功!\n";
	} else	$temp_str = "更新失敗 ! \n";
	$temp_query = "加入 檢測單位organization 欄位 -- by Infodaes (2013-04-01)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


$up_file_name =$upgrade_str."2014-06-12.txt";
if (!is_file($up_file_name)){
	$query = "
CREATE TABLE `fitness_data_swim` (
  c_curr_seme varchar(4) NOT NULL default '',
  student_sn int(10) unsigned NOT NULL default '0',
  test_date date NOT NULL,
  teach_swim tinyint(1) not null,
  swim_class tinyint(1) null,
  swim_score float not null,
  teacher_sn int(10) default NULL,
  up_date timestamp NOT NULL default CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
  PRIMARY KEY (c_curr_seme,student_sn)
) ENGINE=MyISAM;	
	";
	if ($CONN->Execute($query)) {
		$temp_str = "新增游泳資料表成功!\n";
	} else	$temp_str = "新增游泳資料表失敗 ! \n";
	$temp_query = "新增游泳資料表 fitness_data_swim -- by smallduh (2014-06-12)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


$up_file_name =$upgrade_str."2015-04-30.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `fitness_data` CHANGE `up_date` `up_date` DATE NOT NULL DEFAULT '0000-00-00';";
	if ($CONN->Execute($query)) {
		$temp_str = "更改up_date欄位型態為日期成功!\n";
	} else	$temp_str = "更改up_date欄位型態為日期失敗 ! \n";
	$temp_query = "更改up_date欄位型態為日期 -- by infodaes (2015-04-30)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
