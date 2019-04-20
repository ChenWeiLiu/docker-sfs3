
#社團學期設定資料 stud_club_setup 
#  choice_sttime學期開始選課日期, choice_endtime選課結束日期 , choice_num每位學生可選幾個志願
#  choice_over 選志願時, 預選人數是否允許超過社團限制人數
#  choice_auto 依志願排課時, 是否自動把未選課或落選學生排到有空缺名額的社團

CREATE TABLE IF NOT EXISTS `stud_club_setup` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `choice_sttime` datetime NOT NULL,
  `choice_endtime` datetime NOT NULL,
  `choice_num` tinyint(2) unsigned NOT NULL,
  `choice_over` tinyint(1) unsigned NOT NULL,
	`choice_auto` tinyint(1) unsigned NOT NULL,
  `student_num` tinyint(3) unsigned NOT NULL,
  `choice_off` tinyint(1) unsigned NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`),
  KEY `year_seme` (`year_seme`)
) ;



#社團基本資料 club_teacher=teach_base.sn 
# 
#
CREATE TABLE IF NOT EXISTS `stud_club_base` (
  `club_sn` int(10) unsigned NOT NULL auto_increment,
  `year_seme` varchar(4) NOT NULL,
  `club_name` varchar(30) NOT NULL,
  `club_teacher` int(10) unsigned NOT NULL,
  `club_class` tinyint(1) NOT NULL,
  `club_open` tinyint(1) NOT NULL,
  `club_student_num` tinyint(3) unsigned NOT NULL,
  `club_memo` text NOT NULL,
  `update_sn` int(10) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`club_sn`),
  KEY `year_seme` (`year_seme`)
) ;

#社團選修暫存檔
CREATE TABLE IF NOT EXISTS `stud_club_temp` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `club_sn` int(10) unsigned NOT NULL,
  `year_seme` varchar(4) NOT NULL,
  `student_sn` varchar(10) NOT NULL,
  `choice_rank` tinyint(3) unsigned NOT NULL,
  `arranged` tinyint(1) unsigned NOT NULL,
  `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP,
  PRIMARY KEY  (`sn`)
) ;


#社團學生名單與成績
CREATE TABLE IF NOT EXISTS `association` (
  `sn` int(10) unsigned NOT NULL auto_increment,
  `student_sn` varchar(10) NOT NULL,
  `seme_year_seme` varchar(4) NOT NULL,
  `association_name` varchar(40) NOT NULL,
  `score` float NOT NULL,
  `description` text NOT NULL,
  PRIMARY KEY  (`sn`)
) ;

ALTER TABLE `association` ADD `update_sn` int(10) unsigned NOT NULL;
ALTER TABLE `association` ADD `club_sn` INT(10) UNSIGNED NOT NULL;
ALTER TABLE `association` ADD `update_time` timestamp NOT NULL default CURRENT_TIMESTAMP;