<?php
// $Id: module-upgrade.php 7184 2013-03-02 11:41:05Z brucelyc $
                                                                                                               
if(!$CONN){
        echo "go away !!";
        exit;
}
//啟動 session
session_start();                                                                                                        

// 檢查更新否
// 更新記錄檔路徑

$upgrade_path = "upgrade/".get_store_path($path);
$upgrade_str = set_upload_path("$upgrade_path");
$up_file_name =$upgrade_str."2004-12-29.txt";
$update_arr=array("視覺障礙","聽覺障礙","語言障礙","智能障礙","自閉症","肢體障礙","多重障礙","學習障礙","其他身心障礙");
$update_arr2=array("蒙藏生","退伍軍人");
if (!is_file($up_file_name) ){
	$query = "select * from sfs_text where p_id='0' and t_kind='stud_kind'";
	$res = $CONN->Execute($query);
	$p_id = $res->fields[t_id];
	$query = "update sfs_text set t_order_id='99' where p_id='$p_id' and t_name='其他' and p_id='$p_id'";
	$CONN->Execute($query);
	$i=41;
	while(list($k,$v)=each($update_arr)) {
		$query = "select * from sfs_text where t_name='$v'";
		$res=$CONN->Execute($query);
		if (empty($res->fields[t_name])) {
			$query = "insert into sfs_text (t_order_id,t_kind,g_id,d_id,t_name,t_parent,p_id,p_dot) values ('$i','stud_kind','1','$i','$v','".$p_id.","."','$p_id','.')";
			$CONN->Execute($query);
		}
		$i++;
	}
	$i=51;
	while(list($k,$v)=each($update_arr2)) {
		$query = "select * from sfs_text where t_name='$v'";
		$res=$CONN->Execute($query);
		if (empty($res->fields[t_name])) {
			$query = "insert into sfs_text (t_order_id,t_kind,g_id,d_id,t_name,t_parent,p_id,p_dot) values ('$i','stud_kind','1','$i','$v','".$p_id.","."','$p_id','.')";
			$CONN->Execute($query);
		}
		$i++;
	}
	$fp = fopen ($up_file_name, "w");
	$temp_query = "資料表修正 -- by brucelyc (2004-12-29)";
	fwrite($fp,$temp_query);
	fclose($fd);
}


$up_file_name =$upgrade_str."2010-01-11.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists score_semester_move (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		ss_id smallint(5) unsigned NOT NULL default '0',
		score float NOT NULL default '0',
		test_kind varchar(10) NOT NULL default '定期評量',
		test_sort tinyint(3) unsigned NOT NULL default '0',
		enable enum('1','0') NOT NULL default '1',
		update_time datetime NOT NULL default '0000-00-00 00:00:00',
		PRIMARY KEY (year,semester,student_sn,ss_id,test_kind,test_sort)
        )";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立轉入生期中成績表 $ok -- by brucelyc (2010-01-11)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-01-12.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists dis_score_ss (
		ss_id smallint(5) unsigned NOT NULL default '0',
		subject varchar(20) NOT NULL default '',
		year smallint(5) unsigned NOT NULL default '0',
		class_year smallint(5) unsigned NOT NULL default '0',
		PRIMARY KEY (ss_id)
	)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立免試入學科目表 $ok -- by brucelyc (2010-01-12)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-02-18.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists dis_stage (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		subject varchar(20) NOT NULL default '',
		stage varchar(20) NOT NULL default '',
		score float NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,subject,stage)
	)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立免試入學定考成績表 $ok -- by brucelyc (2010-02-18)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-03-08.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists dis_stage_fin (
		year smallint(5) unsigned NOT NULL default '0',
		semester enum('1','2') NOT NULL default '1',
		student_sn int(10) unsigned NOT NULL default '0',
		subject varchar(20) NOT NULL default '',
		stage varchar(20) NOT NULL default '',
		score float NOT NULL default '0',
		PRIMARY KEY (year,semester,student_sn,subject,stage)
	)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立免試入學定考成績封存表 $ok -- by brucelyc (2010-03-08)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2010-03-09.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists `dis_score_fin` (
			`student_sn` int(10) unsigned NOT NULL default '0',
			`year` smallint(5) unsigned NOT NULL default '0',
			`seme` varchar(4) NOT NULL default '',
			`ss_no` int(6) unsigned NOT NULL default '0',
			`score` float NOT NULL default '0.0',
			`pr` int(6) unsigned NOT NULL default '0',
			PRIMARY KEY (student_sn,seme,ss_no)
	)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立免試入學高中職成績封存表 $ok -- by brucelyc (2010-03-09)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-02-18.txt";
if (!is_file($up_file_name) ){
	$query = "select * from sfs_text where p_id='0' and t_kind='stud_kind'";
	$res = $CONN->Execute($query);
	$p_id = $res->fields[t_id];
	$query = "insert into sfs_text (t_order_id,t_kind,g_id,d_id,t_name,t_parent,p_id,p_dot) values ('53','stud_kind','1','53','失業勞工子女','".$p_id.","."','$p_id','.')";
	$CONN->Execute($query);
	$temp_query = "學生身分別新增「失業勞工子女」項目 -- by brucelyc (2011-02-18)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-02-22.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` add `enable0` varchar(1) default '1'";
	$res = $CONN->Execute($query);
	$query = "alter table `stud_seme_dis` add `enable1` varchar(1) default '1'";
	$res = $CONN->Execute($query);
	$query = "alter table `stud_seme_dis` add `enable2` varchar(1) default '1'";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表新增採計學期欄位 -- by brucelyc (2011-02-22)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-02-23.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` add `sp_kind` varchar(1) default ''";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表新增特種身分欄位 -- by brucelyc (2011-02-23)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-02-27.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `dis_score_fin` add `sp_score` float NOT NULL default '0.0'";
	$res = $CONN->Execute($query);
	$query = "alter table `dis_score_fin` add `sp_pr` int(6) NOT NULL default '0'";
	$res = $CONN->Execute($query);
	$temp_query = "免試成績封存資料表新增特種身分分數欄位 -- by brucelyc (2011-02-27)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-02-28.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` add `sp_cal` varchar(1) default NULL";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表新增特殊計算欄位 -- by brucelyc (2011-02-28)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-03-01.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` DROP PRIMARY KEY,ADD PRIMARY KEY (`seme_year_seme`,`student_sn`)";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表修改主鍵值 -- by brucelyc (2011-03-01)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2011-03-09.txt";
if (!is_file($up_file_name) ){
	$creat_table_sql="CREATE TABLE if not exists `dis_score_grad` (
			`student_sn` int(10) unsigned NOT NULL default '0',
			`year` smallint(5) unsigned NOT NULL default '0',
			`seme` varchar(4) NOT NULL default '',
			`ss_no` int(6) unsigned NOT NULL default '0',
			`score` float NOT NULL default '0.0',
			`pr` int(6) unsigned NOT NULL default '0',
			PRIMARY KEY (student_sn,year,seme,ss_no)
	)";
	if ($CONN->Execute($creat_table_sql)) $ok=" 成功 ";
	else $ok="失敗";
	$temp_query = "建立免試入學高中職非應屆成績表 $ok -- by brucelyc (2011-03-09)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2013-02-21.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` add `midincome` varchar(1) default ''";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表新增中低收入戶欄位 -- by brucelyc (2013-02-21)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}

$up_file_name =$upgrade_str."2013-03-01.txt";
if (!is_file($up_file_name) ){
	$query = "alter table `stud_seme_dis` add `su` varchar(1) default '0'";
	$res = $CONN->Execute($query);
	$temp_query = "免試學生資料表新增直升生欄位 -- by brucelyc (2013-02-21)";
	$fd = fopen ($up_file_name, "w");
	fwrite($fd,$temp_query);
	fclose ($fd);
}
?>
