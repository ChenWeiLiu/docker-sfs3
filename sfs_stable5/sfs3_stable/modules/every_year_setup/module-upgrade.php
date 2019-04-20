<?php
// $Id: module-upgrade.php 8485 2015-08-14 15:23:04Z smallduh $
if(!$CONN){
        echo "go away !!";
        exit;
}

// 新增上課日數table seme_course_date

// 檢查更新否
// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2003-08-22.txt";
if (!is_file($up_file_name)){
	$query = "CREATE TABLE seme_course_date (seme_year_seme varchar(6) NOT NULL default '', days tinyint(3) unsigned NOT NULL default '0',`school_days` text NOT NULL, UNIQUE KEY seme_year_seme (seme_year_seme))  COMMENT='學期上課日數'";

	if ($CONN->Execute($query)) 
		$temp_query = "加入學期上課日數資料表 seme_course_date -- by hami (2003-06-08)\n$query";
	else
		$temp_query = "加入學期上課日數資料表 seme_course_date 失敗 !!,請手動更新下列語法\n$query";
		
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

$up_file_name =$upgrade_str."2003-09-26.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `seme_course_date` ADD `class_year` VARCHAR( 3 ) NOT NULL default '' AFTER `seme_year_seme` ;";
	$CONN->Execute($query);
	$CONN->Execute("ALTER TABLE `seme_course_date` DROP INDEX `seme_year_seme` ");
	$query = "ALTER TABLE `seme_course_date` ADD `school_days` text NOT NULL default '';";
	$CONN->Execute($query);	
	
	$query = "ALTER TABLE `seme_course_date` DROP PRIMARY KEY ,ADD PRIMARY KEY ( seme_year_seme, `class_year` ) ";
	
	if ($CONN->Execute($query)) 
		$temp_query = "新增學期上課日數資料表年級欄位  -- by hami (2003-09-26)\n$query";
	else
		$temp_query = "新增學期上課日數資料表年級欄位失敗! \n$query";
		
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

// 增加導師姓名欄位 
$up_file_name =$upgrade_str."2003-10-20.txt";
if (!is_file($up_file_name)){
	$query = array();
	$query[0] = "ALTER TABLE `school_class` ADD `teacher_1` VARCHAR(20) NOT NULL DEFAULT '';";
	$query[1] = "ALTER TABLE `school_class` ADD `teacher_2` VARCHAR(20) NOT NULL DEFAULT '';";
	$temp_str = '';
	for($i=0;$i<count($query);$i++) {	
		if ($CONN->Execute($query[$i]))
			$temp_str .= "$query[$i]\n 更新成功 ! \n";
		else
			$temp_str .= "$query[$i]\n 更新失敗 ! \n";
	}
	$temp_query = "增加導師姓名欄位 -- by brucelyc (2003-10-20)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//取出專科教室名
$up_file_name =$upgrade_str."2004-9-25.txt";
if (!is_file($up_file_name)){
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
	$temp_query = "取出專科教室名 -- by brucelyc (2004-9-25)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//專科教室加入不開放節次
$up_file_name =$upgrade_str."2004-10-22.txt";
if (!is_file($up_file_name)){
	$query=" ALTER TABLE spec_classroom ADD notfree_time  VARCHAR( 250 ) ; ";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 專科教室新增限制節次欄建立成功 ! \n";
	else
		$temp_str = "$query\n 專科教室新增限制節次欄建立失敗 ! \n";

	$temp_query = "專科教室欄位新增 -- by prolin (2004-10-22)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//新建各節上課時間表
$up_file_name =$upgrade_str."2005-05-10.txt";
if (!is_file($up_file_name)){
	$query="CREATE TABLE if not exists section_time (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		sector tinyint(2) unsigned NOT NULL default '0',
		stime varchar(11) NOT NULL default '00:00-00:01',
		PRIMARY KEY (year,semester,sector))";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 各節上課時間表建立成功 ! \n";
	else
		$temp_str = "$query\n 各節上課時間表建立失敗 ! \n";

	$temp_query = "各節上課時間表新建 -- by brucelyc (2005-05-10)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}


//新增教師代碼欄位
$up_file_name =$upgrade_str."2005-07-29.txt";
if (!is_file($up_file_name)){
	$query=" ALTER TABLE `school_class` ADD `tea_sn_1` VARCHAR( 20 ) NOT NULL ,ADD `tea_sn_2` VARCHAR( 20 ) NOT NULL ; ";
//	ALTER TABLE `school_class` CHANGE `year` `year` VARCHAR( 5 ) NOT NULL 
	if ($CONN->Execute($query))
		$temp_str = "$query\n 新增教師代碼欄位建立成功 ! \n";
	else
		$temp_str = "$query\n 新增教師代碼欄位建立失敗 ! \n";

	$temp_query = "新增教師代碼欄位 -- by chi (2005-07-29)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

//修正成績設定錯誤
$up_file_name =$upgrade_str."2008-06-12.txt";
if (!is_file($up_file_name)){
        $query="update score_setup set rule='優_>=_90\n甲_>=_80\n乙_>=_70\n丙_>=_60\n丁_<_60' where rule like '優_>=90%'";
        if ($CONN->Execute($query))
                $temp_str = "$query\n 修正成績設定錯誤成功 ! \n";
        else
                $temp_str = "$query\n 修正成績設定錯誤失敗 ! \n";

        $temp_query = "修正成績設定錯誤 -- by brucelyc (2008-06-12)\n\n$temp_str";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fp);
}

//修正資料表primary key
$up_file_name =$upgrade_str."2008-10-17.txt";
if (!is_file($up_file_name)){
        $query="ALTER TABLE `seme_course_date` DROP primary key";
		$CONN->Execute($query);
		$query="ALTER TABLE `seme_course_date` ADD primary key (seme_year_seme,class_year)";
        if ($CONN->Execute($query))
                $temp_str = "$query\n 修正primary key成功 ! \n";
        else
                $temp_str = "$query\n 修正primary key失敗 ! \n";

        $temp_query = "修正primary key -- by brucelyc (2008-10-17)\n\n$temp_str";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fp);
}

$up_file_name =$upgrade_str."2012-09-10.txt";
if (!is_file($up_file_name)){
        $query="ALTER TABLE `score_course` CHANGE `room` `room` VARCHAR(20);";
        if ($CONN->Execute($query))
                $temp_str = "$query\n 修正 專科教室名稱長度為20 成功 ! \n";
        else
                $temp_str = "$query\n 修正 專科教室名稱長度為20 失敗 ! \n";

        $temp_query = "修正 專科教室名稱長度為20  -- by infodaes (2012-09-10)\n\n$temp_str";
        $fp = fopen ($up_file_name, "w");
        fwrite($fp,$temp_query);
        fclose ($fp);
}

//新增協同教學教師代碼欄位
$up_file_name =$upgrade_str."2014-08-31.txt";
if (!is_file($up_file_name)){
	$query="ALTER TABLE `score_course` ADD `cooperate_sn` INT NULL AFTER `teacher_sn`; ";
	if ($CONN->Execute($query))
		$temp_str = "$query\n 新增協同教學教師代碼欄位建立成功 ! \n";
	else
		$temp_str = "$query\n 新增協同教學教師代碼欄位建立失敗 ! \n";

	$temp_query = "新增協同教學教師代碼欄位 -- by chi (2014-08-31)\n\n$temp_str";
	$fp = fopen ($up_file_name, "w");
	fwrite($fp,$temp_query);
	fclose ($fp);
}

?>
