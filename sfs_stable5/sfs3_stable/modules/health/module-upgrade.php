<?php
// $Id: module-upgrade.php 6534 2011-09-22 09:46:05Z infodaes $

if(!$CONN){
        echo "go away !!";
        exit;
}

// 更新記錄檔路徑
$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");

$up_file_name =$upgrade_str."2008-09-01.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `health_checks_record` DROP PRIMARY KEY,ADD PRIMARY KEY (year,semester,student_sn,subject,no)";
	if ($CONN->Execute($query)) {
		$temp_query = "更新定期健檢記錄表的主要鍵 -- by brucelyc (2008-09-01)\n$query";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2008-09-02.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_fday (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		week_no smallint(5) unsigned NOT NULL default '0',
		do_date date NOT NULL default '0000-00-00',
		PRIMARY KEY (year,semester,week_no)
		)";
	if ($CONN->Execute($creat_table_sql)) {
		$temp_query = "建立含氟漱口水實施日期表 -- by brucelyc (2008-09-02)";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2008-09-03.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_frecord (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		agree char(1) NOT NULL default '',
		w1 char(1) NOT NULL default '',
		w2 char(1) NOT NULL default '',
		w3 char(1) NOT NULL default '',
		w4 char(1) NOT NULL default '',
		w5 char(1) NOT NULL default '',
		w6 char(1) NOT NULL default '',
		w7 char(1) NOT NULL default '',
		w8 char(1) NOT NULL default '',
		w9 char(1) NOT NULL default '',
		w10 char(1) NOT NULL default '',
		w11 char(1) NOT NULL default '',
		w12 char(1) NOT NULL default '',
		w13 char(1) NOT NULL default '',
		w14 char(1) NOT NULL default '',
		w15 char(1) NOT NULL default '',
		w16 char(1) NOT NULL default '',
		w17 char(1) NOT NULL default '',
		w18 char(1) NOT NULL default '',
		w19 char(1) NOT NULL default '',
		w20 char(1) NOT NULL default '',
		w21 char(1) NOT NULL default '',
		w22 char(1) NOT NULL default '',
		w23 char(1) NOT NULL default '',
		w24 char(1) NOT NULL default '',
		w25 char(1) NOT NULL default '',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立含氟漱口水實施記錄表 $ok -- by brucelyc (2008-09-02)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."accident.txt";
if (!is_file($up_file_name)){
	include_once "../../include/sfs_case_sql.php";
	$query="select * from health_accident_place where 1=0";
	if (!$CONN->Execute($query)) {
		$creat_table_sql="CREATE TABLE health_accident_place (
		id int(6) unsigned NOT NULL default '1',
		name varchar(100) NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (id)
		) ;
		INSERT INTO health_accident_place VALUES ( 1,'操場',1);
		INSERT INTO health_accident_place VALUES ( 2,'遊戲運動器材',1);
		INSERT INTO health_accident_place VALUES ( 3,'普通教室',1);
		INSERT INTO health_accident_place VALUES ( 4,'專科教室',1);
		INSERT INTO health_accident_place VALUES ( 5,'走廊',1);
		INSERT INTO health_accident_place VALUES ( 6,'樓梯',1);
		INSERT INTO health_accident_place VALUES ( 7,'地下室',1);
		INSERT INTO health_accident_place VALUES ( 8,'體育館活動中心',1);
		INSERT INTO health_accident_place VALUES ( 9,'廁所',1);
		INSERT INTO health_accident_place VALUES ( 10,'校外',1);
		INSERT INTO health_accident_place VALUES ( 999,'其他',1);";
		run_sql($creat_table_sql, $mysql_db);
	}

	$query="select * from health_accident_reason where 1=0";
	if (!$CONN->Execute($query)) {
		$creat_table_sql="CREATE TABLE health_accident_reason (
		id int(6) unsigned NOT NULL default '1',
		name varchar(100) NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (id)
		) ;
		INSERT INTO health_accident_reason VALUES ( 1,'下課遊戲',1);
		INSERT INTO health_accident_reason VALUES ( 2,'上下課途中',1);
		INSERT INTO health_accident_reason VALUES ( 3,'升旗',1);
		INSERT INTO health_accident_reason VALUES ( 4,'打破玻璃',1);
		INSERT INTO health_accident_reason VALUES ( 5,'打掃',1);
		INSERT INTO health_accident_reason VALUES ( 6,'上下樓梯',1);
		INSERT INTO health_accident_reason VALUES ( 7,'藝能課',1);
		INSERT INTO health_accident_reason VALUES ( 8,'體育課',1);
		INSERT INTO health_accident_reason VALUES ( 999,'其他',1);";
		run_sql($creat_table_sql, $mysql_db);
	}

	$query="select * from health_accident_part where 1=0";
	if (!$CONN->Execute($query)) {
		$creat_table_sql="CREATE TABLE health_accident_part (
		id int(6) unsigned NOT NULL default '1',
		name varchar(100) NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (id)
		) ;
		INSERT INTO health_accident_part VALUES ( 1,'頭',1);
		INSERT INTO health_accident_part VALUES ( 2,'頸',1);
		INSERT INTO health_accident_part VALUES ( 3,'肩',1);
		INSERT INTO health_accident_part VALUES ( 4,'胸',1);
		INSERT INTO health_accident_part VALUES ( 5,'腹',1);
		INSERT INTO health_accident_part VALUES ( 6,'背',1);
		INSERT INTO health_accident_part VALUES ( 7,'眼',1);
		INSERT INTO health_accident_part VALUES ( 8,'顏面',1);
		INSERT INTO health_accident_part VALUES ( 9,'口腔',1);
		INSERT INTO health_accident_part VALUES ( 10,'耳鼻喉',1);
		INSERT INTO health_accident_part VALUES ( 11,'上肢',1);
		INSERT INTO health_accident_part VALUES ( 12,'腰',1);
		INSERT INTO health_accident_part VALUES ( 13,'下肢',1);
		INSERT INTO health_accident_part VALUES ( 14,'臀部',1);
		INSERT INTO health_accident_part VALUES ( 15,'會陰部',1);";
		run_sql($creat_table_sql, $mysql_db);
	}
	$query="select * from health_accident_status where 1=0";
	if (!$CONN->Execute($query)) {
		$creat_table_sql="CREATE TABLE health_accident_status (
		id int(6) unsigned NOT NULL default'1',
		name varchar(100) NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (id)
		) ;
		INSERT INTO health_accident_status VALUES ( 1,'擦傷',1);
		INSERT INTO health_accident_status VALUES ( 2,'裂割刺傷',1);
		INSERT INTO health_accident_status VALUES ( 3,'夾壓傷',1);
		INSERT INTO health_accident_status VALUES ( 4,'挫撞傷',1);
		INSERT INTO health_accident_status VALUES ( 5,'扭傷',1);
		INSERT INTO health_accident_status VALUES ( 6,'灼燙傷',1);
		INSERT INTO health_accident_status VALUES ( 7,'叮咬傷',1);
		INSERT INTO health_accident_status VALUES ( 8,'骨折',1);
		INSERT INTO health_accident_status VALUES ( 9,'舊傷',1);
		INSERT INTO health_accident_status VALUES ( 10,'外科其他',1);
		INSERT INTO health_accident_status VALUES ( 11,'發燒',1);
		INSERT INTO health_accident_status VALUES ( 12,'暈眩',1);
		INSERT INTO health_accident_status VALUES ( 13,'噁心嘔吐',1);
		INSERT INTO health_accident_status VALUES ( 14,'頭痛',1);
		INSERT INTO health_accident_status VALUES ( 15,'牙痛',1);
		INSERT INTO health_accident_status VALUES ( 16,'胃痛',1);
		INSERT INTO health_accident_status VALUES ( 17,'腹痛',1);
		INSERT INTO health_accident_status VALUES ( 18,'腹瀉',1);
		INSERT INTO health_accident_status VALUES ( 19,'經痛',1);
		INSERT INTO health_accident_status VALUES ( 20,'氣喘',1);
		INSERT INTO health_accident_status VALUES ( 21,'流鼻血',1);
		INSERT INTO health_accident_status VALUES ( 22,'疹癢',1);
		INSERT INTO health_accident_status VALUES ( 23,'眼疾',1);
		INSERT INTO health_accident_status VALUES ( 24,'內科其他',1);";
		run_sql($creat_table_sql, $mysql_db);
	}

	$query="select * from health_accident_attend where 1=0";
	if (!$CONN->Execute($query)) {
		$creat_table_sql="CREATE TABLE health_accident_attend (
		id int(6) unsigned NOT NULL default'1',
		name varchar(100) NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (id)
		) ;
		INSERT INTO health_accident_attend VALUES ( 1,'傷口處理',1);
		INSERT INTO health_accident_attend VALUES ( 2,'冰敷',1);
		INSERT INTO health_accident_attend VALUES ( 3,'熱敷',1);
		INSERT INTO health_accident_attend VALUES ( 4,'休息觀察',1);
		INSERT INTO health_accident_attend VALUES ( 5,'通知家長',1);
		INSERT INTO health_accident_attend VALUES ( 6,'家長帶回',1);
		INSERT INTO health_accident_attend VALUES ( 7,'校方送醫',1);
		INSERT INTO health_accident_attend VALUES ( 8,'衛生教育',1);
		INSERT INTO health_accident_attend VALUES ( 999,'其他處理',1);";
		run_sql($creat_table_sql, $mysql_db);
	}

	$creat_table_sql="CREATE TABLE if not exists health_accident_record (
	id int(10) unsigned NOT NULL auto_increment,
	year smallint(5) unsigned NOT NULL default '0',
	semester enum('0','1','2') NOT NULL default '0',
	student_sn int(10) unsigned NOT NULL default '0',
	sign_time datetime NOT NULL default '0000-00-00 00:00:00',
	obs_min int(6) unsigned NOT NULL default '0',
	temp decimal(3,1) NOT NULL default '0.0',
	place_id int(6) unsigned NOT NULL default '0',
	reason_id int(6) unsigned NOT NULL default '0',
	memo text NOT NULL default '',
	update_date timestamp,
	teacher_sn int(11) NOT NULL default '0',
	PRIMARY KEY (id)
	) ;";
	run_sql($creat_table_sql, $mysql_db);

	$creat_table_sql="CREATE TABLE if not exists health_accident_part_record (
	pid int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	part_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (pid)
	) ;";
	run_sql($creat_table_sql, $mysql_db);

	$creat_table_sql="CREATE TABLE if not exists health_accident_status_record (
	sid int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	status_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (sid)
	) ;";
	run_sql($creat_table_sql, $mysql_db);

	$creat_table_sql="CREATE TABLE if not exists health_accident_attend_record (
	aid int(10) unsigned NOT NULL auto_increment,
	id int(10) unsigned NOT NULL default '0',
	attend_id int(6) unsigned NOT NULL default '0',
	PRIMARY KEY (aid)
	) ;";
	run_sql($creat_table_sql, $mysql_db);

	$msg = "補建傷病資料表 -- by brucelyc (2008-09-12)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2008-10-08.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_checks_doctor (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		subject varchar(50) NOT NULL default'',
		hospital varchar(100) NOT NULL default'',
		doctor varchar(50) NOT NULL default'',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,subject)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立健檢醫院醫師記錄表 $ok -- by brucelyc (2008-10-08)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2008-10-14.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `health_checks_doctor` add cyear tinyint(4) not NULL default '0'";
	if ($CONN->Execute($query)) {
		$temp_query = "於健檢醫院醫師記錄表中加入年級 -- by brucelyc (2008-10-14)\n$query";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2008-10-15.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `health_checks_doctor` add measure_date date NOT NULL default '0000-00-00'";
	if ($CONN->Execute($query)) {
		$temp_query = "於健檢醫院醫師記錄表中加入檢查時間 -- by brucelyc (2008-10-15)\n$query";
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2008-10-30.txt";
if (!is_file($up_file_name)){
	$query = "ALTER TABLE `health_inject_record` add kid tinyint(4) unsigned NOT NULL default 0";
	if ($CONN->Execute($query)) {
		$temp_query = "於預防注射記錄表中加入kid -- by brucelyc (2008-10-30)\n$query";
		$CONN->Execute("ALTER TABLE `health_inject_record` drop primary key");
		$CONN->Execute("ALTER TABLE `health_inject_record` add primary key (student_sn,kid,id)");
		$fd = fopen ($up_file_name, "w");
		fwrite($fd,$temp_query);
		fclose ($fd);
	}
}

$up_file_name =$upgrade_str."2009-04-15.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_manage_record (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		tbl varchar(50) NOT NULL default '',
		item varchar(20) NOT NULL default '',
		id int(10) unsigned NOT NULL default '0',
		memo text NOT NULL default '',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,tbl,item,id)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立處置記錄表 $ok -- by brucelyc (2009-04-15)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-04-16.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_diag_record (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		tbl varchar(50) NOT NULL default '',
		item varchar(20) NOT NULL default '',
		id int(10) unsigned NOT NULL default '0',
		memo text NOT NULL default '',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,tbl,item,id)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立診斷記錄表 $ok -- by brucelyc (2009-04-16)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."yellow_card.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE health_yellowcard (
		student_sn int(10) unsigned NOT NULL default '0',
		value tinyint(1) unsigned NOT NULL default '0',
		PRIMARY KEY (student_sn)
		)";
	$query="select * from health_yellowcard where 1=0";
	$res=$CONN->Execute($query);
	if ($res) $ok="資料表已存在";
	elseif ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立含氟漱口水實施記錄表 -- $ok -- by brucelyc (2009-06-18)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-09-01.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_disease_report (
		id int(10) unsigned NOT NULL auto_increment,
		student_sn int(10) unsigned NOT NULL default '0',
		dis_date date NOT NULL default '0000-00-00',
		dis_kind int(6) unsigned NOT NULL default '0',
		sym_str text NOT NULL default '',
		status varchar(2) NOT NULL default '',
		diag_date date NOT NULL default '0000-00-00',
		diag_hos varchar(50) NOT NULL default '',
		diag_name varchar(50) NOT NULL default '',
		chk_date date NOT NULL default '0000-00-00',
		chk_report varchar(50) NOT NULL default '',
		update_date timestamp,
		oth_chk text NOT NULL default '',
		oth_txt text NOT NULL default '',
		teacher_sn int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (student_sn,dis_date),
		KEY `id` (`id`)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立流感通報表 $ok -- by brucelyc (2009-09-01)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-09-02.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_inflection_item (
		iid int(10) unsigned NOT NULL default '0',
		name varchar(50) NOT NULL default '',
		memo text NOT NULL default '',
		enable varchar(1) NOT NULL default '1',
		PRIMARY KEY (iid)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (1,'類流感','急性呼吸道感染且具有下列症狀：1.突然發病有發燒（耳溫≧38℃）及呼吸道感染 2.且有肌肉酸痛或頭痛或極度厭倦感',1)");
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (2,'手足口病或&#30129;疹性咽峽炎','手足口病：口、手掌、腳掌及或膝蓋、臀部出現小水泡或紅疹；疹性咽峽炎：發燒且咽部出現小水泡或潰瘍',1)");
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (3,'腹瀉','每日腹瀉三次以上，且合併下列任何一項以上：1.嘔吐 2.發燒 3.黏液液狀或血絲 4.水瀉',1)");
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (4,'發燒','發燒（耳溫≧38℃）且未有前述疾病或症狀',1)");
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (5,'紅眼症','眼睛刺痛、灼熱、怕光、易流淚、霧視；眼結膜呈鮮紅色，有時會有結膜下出血；眼睛產生大量黏性分泌物；有時耳前淋巴結腫大、壓痛',1)");
	$CONN->Execute("INSERT INTO health_inflection_item VALUES (99,'其他','前列項目外之特殊傳染病',1)");
	$temp_query = "建立疑似傳染病項目表 $ok -- by brucelyc (2009-09-02)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-09-03.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_inflection_record (
		id int(10) unsigned NOT NULL auto_increment,
		student_sn int(10) unsigned NOT NULL default '0',
		iid int(10) unsigned NOT NULL default '0',
		dis_date date NOT NULL default '0000-00-00',
		weekday int(4) unsigned NOT NULL default '0',
		status varchar(2) NOT NULL default '',
		rmemo text NOT NULL default '',
		update_date timestamp,
		teacher_sn int(10) unsigned NOT NULL default '0',
		PRIMARY KEY (student_sn,dis_date),
		KEY `id` (`id`)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立疑似傳染病記錄表 $ok -- by brucelyc (2009-09-03)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-09-07.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE if not exists health_status_record (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		tbl varchar(50) NOT NULL default '',
		item varchar(20) NOT NULL default '',
		id int(10) unsigned NOT NULL default '0',
		memo text NOT NULL default '',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,tbl,item,id)
		)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立陳述記錄表 $ok -- by brucelyc (2009-09-07)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-10-19.txt";
if (!is_file($up_file_name)){
	if ($CONN->Execute("INSERT INTO health_inject_item VALUES ( 8,'水痘疫苗','',1,0,'','','','','',1,'');"))
		$ok=" 成功 ";
	else
		$ok="失敗";
	$temp_query = "新增水痘疫苗紀錄 $ok -- by brucelyc (2009-10-19)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2009-10-27.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE health_sight_co (
		student_sn int(10) unsigned NOT NULL default '0',
		co varchar(1) NOT NULL default '',
		update_date timestamp,
		teacher_sn int(11) NOT NULL default '0',
		PRIMARY KEY (student_sn)
	) ;";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立色盲記錄表 $ok -- by brucelyc (2009-10-27)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-03-23.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="ALTER TABLE health_sight ADD hospital varchar(30)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "加入就診醫療院所 $ok -- by hami (2010-03-23)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-04-6.txt";
if (!is_file($up_file_name)){
	$creat_table_sql="CREATE TABLE IF NOT EXISTS `health_inject_record` (
  `student_sn` int(10) unsigned NOT NULL DEFAULT '0',
  `id` int(6) unsigned NOT NULL DEFAULT '0',
  `times` int(4) unsigned NOT NULL DEFAULT '0',
  `date0` date NOT NULL DEFAULT '0000-00-00',
  `date1` date NOT NULL DEFAULT '0000-00-00',
  `date2` date NOT NULL DEFAULT '0000-00-00',
  `date3` date NOT NULL DEFAULT '0000-00-00',
  `date4` date NOT NULL DEFAULT '0000-00-00',
  `update_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `teacher_sn` int(11) NOT NULL DEFAULT '0',
  `kid` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`student_sn`,`kid`,`id`)
);";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "補建立 health_inject_record  $ok -- by hami (2010-03-23)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-10-22.txt";
if (!is_file($up_file_name)){
        $query="show columns from health_inject_item";
        $res=$CONN->Execute($query);
        $OK=0;
        while(!$res->EOF) {
                if ($res->fields['Field']=="code") $OK=1;
                $res->MoveNext();
        }
        if ($OK==0) {
		$query="ALTER TABLE `health_inject_item` add `code` varchar(10) not NULL default ''";
		$CONN->Execute($query);
	}
	$arr=array();
	$arr=array("1"=>"BCG","2"=>"HepB","3"=>"OPV","4"=>"DPT","5"=>"JE","6"=>"MV","7"=>"MMR"); //DPT: 白喉百日咳破傷風混合疫苗, Td: 破傷風減量白喉混合疫苗
	foreach($arr as $k=>$v) {
		$query="update health_inject_item set code='$v' where id='$k'";
		$CONN->Execute($query);
	}
	$temp_query = "補疫苗代碼 -- by brucelyc (2010-10-22)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}
