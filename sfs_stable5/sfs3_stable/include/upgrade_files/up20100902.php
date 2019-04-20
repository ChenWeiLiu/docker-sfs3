<?php

//$Id: up20100815.php 5983 2010-08-15 19:13:56Z brucelyc $

if(!$CONN){
        echo "go away !!";
        exit;
}
	//創建新的學生異動學期編班資料表
	$SQL="CREATE TABLE IF NOT EXISTS `stud_seme_import` (
  `seme_year_seme` varchar(6) NOT NULL default '',
  `stud_id` varchar(20) NOT NULL default '',
  `seme_class_grade` varchar(10) default NULL,
  `seme_class_name` varchar(10) default NULL,
  `seme_num` tinyint(3) unsigned default NULL,
  `seme_class_year_s` int(10) unsigned default NULL,
  `seme_class_s` tinyint(3) unsigned default NULL,
  `seme_num_s` tinyint(3) unsigned default NULL,
  `student_sn` int(10) unsigned NOT NULL,
  PRIMARY KEY  (`seme_year_seme`,`stud_id`))";
	$rs = $CONN->Execute($SQL);
	
	//部分學校已經產生過stud_seme_import，所以另外執行SQL補teacher_name
	$SQL="ALTER TABLE `stud_seme_import` ADD `teacher_name` VARCHAR( 20 ) ;";
	$rs = $CONN->Execute($SQL);
	
	
	$SQL="CREATE TABLE IF NOT EXISTS `stud_move_import` (
  `move_id` bigint(20) NOT NULL auto_increment,
  `stud_id` varchar(20) NOT NULL default '',
  `move_kind` varchar(10) NOT NULL default '',
  `move_year_seme` varchar(6) NOT NULL default '',
  `school_move_num` int(11) default NULL,
  `move_date` date NOT NULL default '0000-00-00',
  `move_c_unit` varchar(30) default NULL,
  `move_c_date` date default '0000-00-00',
  `move_c_word` varchar(20) default NULL,
  `move_c_num` varchar(14) default NULL,
  `update_time` datetime NOT NULL default '0000-00-00 00:00:00',
  `update_id` varchar(20) NOT NULL default '',
  `update_ip` varchar(15) NOT NULL default '',
  `school` varchar(40) NOT NULL default '',
  `school_id` varchar(6) default NULL,
  `student_sn` int(10) unsigned NOT NULL default '0',
  `reason` text NOT NULL,
  `city` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`move_id`))";
  $CONN->Execute($SQL);
  
		$SQL="CREATE TABLE IF NOT EXISTS `stud_psy_test` ( 
		  `sn` int(10) unsigned NOT NULL auto_increment,
		  `year` tinyint(4) unsigned NOT NULL default '0',
		  `semester` tinyint(4) unsigned NOT NULL default '0',
		  `test_date` date default NULL,
		  `student_sn` int(10) unsigned NOT NULL default '0',
		  `item` varchar(60) default NULL,
		  `score` varchar(40) default NULL,
		  `model` varchar(40) default NULL,
		  `standard` varchar(40) default NULL,
		  `pr` varchar(40) default NULL,
		  `explanation` varchar(100) default NULL,
		  `teacher_sn` int(10) unsigned NOT NULL default '0',
		  `update_time` datetime default NULL,
		  PRIMARY KEY  (`sn`) )";
		$rs = $CONN->Execute($SQL);

?>
