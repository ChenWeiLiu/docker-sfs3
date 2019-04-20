<?php
// $Id: module-upgrade.php 7517 2013-09-12 21:51:42Z hami $
if(!$CONN){
        echo "go away !!";
        exit;
}

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

//取出專科教室名
$up_file_name =$upgrade_str."2004-9-25.txt";
if (!is_file($up_file_name)){
	$query="select * from spec_classroom where 1=0";
	if ($CONN->Execute($query))
		$temp_str = "專科教室表已存在, 無需升級。";
	else {
		$query="CREATE TABLE if not exists spec_classroom (
			room_id smallint(5) unsigned NOT NULL auto_increment,
			room_name varchar(20) NOT NULL default '',
			enable enum('0','1') NOT NULL default '1',
			PRIMARY KEY (room_id))";
		if ($CONN->Execute($query))
			$temp_str = "$query\n 專科教室表建立成功 ! \n";
		else
			$temp_str = "$query\n 專科教室表建立失敗 ! \n";
		$query="select distinct room from score_course order by room";
		$res=$CONN->Execute($query);
		while(!$res->EOF) {
			if (addslashes($res->fields[room])) {
				$query_insert="insert into spec_classroom (room_name) values ('".addslashes($res->fields[room])."')";
				$CONN->Execute($query_insert);
			}
			$res->MoveNext();
		}
	}
	$temp_query = "取出專科教室名 -- by brucelyc (2004-9-25)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//專科教室加入不開放節次
$up_file_name =$upgrade_str."2004-10-22.txt";
if (!is_file($up_file_name)){
	$query="ALTER TABLE `spec_classroom` ADD `notfree_time` VARCHAR(250)";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 專科教室新增限制節次欄建立成功 ! \n";
	else
		$temp_str = "$query\n 專科教室新增限制節次欄建立失敗 ! \n";

	$temp_query = "專科教室欄位新增 -- by prolin (2004-10-22)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//專科教室加入預約班級
$up_file_name =$upgrade_str."2005-10-13.txt";
if (!is_file($up_file_name)){
	$query="ALTER TABLE `course_room` ADD `seme_class` VARCHAR(10) NOT NULL default ''";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 專科教室新增預約班級欄建立成功 ! \n";
	else
		$temp_str = "$query\n 專科教室新增預約班級欄建立失敗 ! \n";

	$temp_query = "專科教室欄位新增 -- by brucelyc (2005-10-13)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//加入多欄位唯一索引

$up_file_name =$upgrade_str."2013-9-13.txt";
if (!is_file($up_file_name)){
	$query ="ALTER TABLE `course_room` drop index conflict;";
	$CONN->Execute($query);
	$query="ALTER TABLE `course_room` ADD UNIQUE `conflict` ( `date` , `sector` , `room` );";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 專科教室新增唯一索引欄建立成功 ! \n";
	else
		$temp_str = "$query\n 專科教室新增唯一索引欄建立失敗 ! \n";

	$temp_query = "專科教室唯一索引欄新增 -- by infodaes (2006-3-16) \n\n 先刪除索引再建立(hami 2013-09-13) $temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}
?>
