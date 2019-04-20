#
# 資料表格式： `grad_stud`
#

CREATE TABLE grad_stud (
  grad_sn int(10) NOT NULL auto_increment,
  stud_grad_year tinyint(3) unsigned default NULL,
  class_year char(2) default NULL,
  class_sort tinyint(2) unsigned default NULL,
  stud_id varchar(20) default NULL,
  grad_kind tinyint(1) unsigned default NULL,
  grad_date date default NULL,
  grad_word varchar(20) default NULL,
  grad_num varchar(20) default NULL,
  grad_score float unsigned default NULL,
  UNIQUE KEY grad_sn (grad_sn)
) TYPE=MyISAM;

#
# 資料表格式： `school_day`
#

CREATE TABLE school_day (
  day_kind varchar(40) NOT NULL default '',
  day date NOT NULL default '0000-00-00',
  year tinyint(2) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  UNIQUE KEY year_seme (day_kind,year,seme)
) TYPE=MyISAM;

#
# 列出以下資料庫的數據： `school_day`
#

#
# 資料表格式： `stud_absence`
#

CREATE TABLE stud_absence (
  abs_sn bigint(20) NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  stud_id varchar(20) NOT NULL default '',
  ab1 tinyint(1) default NULL,
  ab2 tinyint(1) default NULL,
  ab3 tinyint(1) default NULL,
  ab4 tinyint(1) default NULL,
  ab5 tinyint(1) default NULL,
  ab6 tinyint(1) default NULL,
  ab7 tinyint(1) default NULL,
  meno varchar(200) default NULL,
  PRIMARY KEY  (abs_sn)
) TYPE=MyISAM;

#
# 列出以下資料庫的數據： `stud_absence`
#

ALTER TABLE `board_check` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE `board_p` CHANGE `b_url` `b_url` VARCHAR( 150 ) NOT NULL ;
ALTER TABLE `board_p` ADD `teacher_sn` SMALLINT UNSIGNED NOT NULL ;
ALTER TABLE `docup_p` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `docup_p` CHANGE `doc_kind_id` `doc_kind_id` INT DEFAULT '0' NOT NULL ;
ALTER TABLE `docup` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL ;
ALTER TABLE `score_setup` ADD `allow_modify` ENUM( 'false', 'true' ) NOT NULL ;
ALTER TABLE `score_ss` ADD `class_id` VARCHAR( 11 ) NOT NULL ,ADD `link_ss` VARCHAR( 200 ) NOT NULL ;
ALTER TABLE `sfs_text` CHANGE `d_id` `d_id` VARCHAR( 20 ) DEFAULT '0' NOT NULL ;


DROP TABLE IF EXISTS stud_absent;

CREATE TABLE stud_absent (
  sasn int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  class_id varchar(11) NOT NULL default '',
  stud_id varchar(20) NOT NULL default '',
  date date NOT NULL default '0000-00-00',
  absent_kind varchar(20) NOT NULL default '',
  section varchar(10) NOT NULL default '',
  sign_man_sn int(11) NOT NULL default '0',
  sign_man_name varchar(20) NOT NULL default '',
  sign_time datetime NOT NULL default '0000-00-00 00:00:00',
  txt text NOT NULL,
  PRIMARY KEY  (sasn),
  UNIQUE KEY date (stud_id,date,section),
  KEY year (year,semester,class_id,stud_id),
  KEY sign_man_sn (sign_man_sn)
) TYPE=MyISAM;


