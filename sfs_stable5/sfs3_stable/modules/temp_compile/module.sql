# $Id: module.sql 8153 2014-09-30 01:33:20Z smallduh $
# 資料表格式： `new_stud`
#

CREATE TABLE new_stud (
  newstud_sn int(10) NOT NULL auto_increment,
  stud_study_year tinyint(3) unsigned default NULL,
  old_school varchar(100) default NULL,
  stud_person_id varchar(20) default NULL,
  stud_name varchar(20) default NULL,
  stud_sex tinyint(3) unsigned default NULL,
  stud_tel_1 varchar(20) default NULL,
  stud_birthday date default NULL,
  guardian_name varchar(20) default NULL,
  stud_address varchar(200) default NULL,
  sure_study enum('1','0') default NULL,
  stud_id varchar(20) default NULL,
  class_year char(2) default NULL,
  class_sort tinyint(2) unsigned default NULL,
  class_site tinyint(2) unsigned default NULL,
  temp_score1 tinyint(4) NOT NULL default '-100',
  temp_score2 tinyint(4) NOT NULL default '-100',
  temp_score3 tinyint(4) NOT NULL default '-100',
  meno varchar(200) NOT NULL default '',
  old_class varchar(20) default NULL,
  temp_id varchar(6) default NULL,
  temp_class char(3) default NULL,
  temp_site smallint(5) default 0,
  addr_zip char(3) default NULL,
  oth_class char(3) default NULL,
  oth_site smallint(5) default 0,
  sure_oth enum('1','0') default 0,
  sort_sn smallint(3) unsigned default NULL,
  class_meno varchar(50) NOT NULL default '',
  UNIQUE KEY newstud_sn (newstud_sn)
) ENGINE=MyISAM;

CREATE TABLE  if not exists `new_stud_notification` (
`note_sn` INT NOT NULL AUTO_INCREMENT ,
`year` INT( 3 ) UNSIGNED NOT NULL ,
`org` VARCHAR( 50 ) NOT NULL ,
`num` VARCHAR( 50 ) NOT NULL ,
`c_place` VARCHAR( 50 ) NOT NULL ,
`c_date` VARCHAR( 50 ) NOT NULL ,
`c_time` VARCHAR( 50 ) NOT NULL ,
`p_date` VARCHAR( 50 ) NOT NULL ,
`p_time` VARCHAR( 50 ) NOT NULL ,
`note` TEXT DEFAULT NULL ,
`class_year` CHAR(2) ,
`note2` TEXT ,
PRIMARY KEY ( `note_sn` ) ,
UNIQUE (`year`)
) ENGINE=MyISAM;
