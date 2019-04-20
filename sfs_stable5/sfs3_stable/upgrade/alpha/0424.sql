#
# ��ƪ��榡�G `board_check`
#

CREATE TABLE board_check (
  pc_id int(11) NOT NULL auto_increment,
  pro_kind_id varchar(12) NOT NULL default '0',
  post_office tinyint(4) NOT NULL default '-1',
  teach_id varchar(20) NOT NULL default 'none',
  teach_title_id tinyint(4) NOT NULL default '-1',
  is_admin char(1) NOT NULL default '',
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY  (pc_id)
) TYPE=MyISAM;

#
# �C�X�H�U��Ʈw���ƾڡG `board_check`
#

INSERT INTO board_check VALUES (1, 'office1', 0, 'none', 0, '', 1);
INSERT INTO board_check VALUES (2, 'office2', 0, 'none', 0, '', 1);
# --------------------------------------------------------

#
# ��ƪ��榡�G `board_kind`
#

CREATE TABLE board_kind (
  bk_id varchar(12) NOT NULL default '0',
  board_name varchar(20) NOT NULL default '',
  board_date date NOT NULL default '0000-00-00',
  board_k_id char(1) NOT NULL default '',
  board_last_date date NOT NULL default '0000-00-00',
  board_is_upload char(1) NOT NULL default '',
  board_is_public char(1) NOT NULL default '',
  board_admin varchar(100) NOT NULL default '',
  PRIMARY KEY  (bk_id)
) TYPE=MyISAM;

#
# �C�X�H�U��Ʈw���ƾڡG `board_kind`
#

INSERT INTO board_kind VALUES ('office1', '�аȳB', '2003-04-28', '0', '0000-00-00', '1', '1', '');
INSERT INTO board_kind VALUES ('office2', '�V�ɳB', '2003-04-28', '0', '0000-00-00', '1', '1', '');
# --------------------------------------------------------

#
# ��ƪ��榡�G `board_p`
#

CREATE TABLE board_p (
  b_id bigint(20) unsigned NOT NULL auto_increment,
  bk_id varchar(12) NOT NULL default '0',
  b_open_date date NOT NULL default '0000-00-00',
  b_days smallint(6) NOT NULL default '0',
  b_unit varchar(20) NOT NULL default '',
  b_title varchar(30) NOT NULL default '',
  b_name varchar(20) NOT NULL default '',
  b_sub varchar(60) NOT NULL default '',
  b_con text NOT NULL,
  b_hints smallint(6) NOT NULL default '0',
  b_upload varchar(60) NOT NULL default '',
  b_url varchar(150) NOT NULL default '',
  b_post_time datetime default NULL,
  b_own_id varchar(20) NOT NULL default '',
  b_is_intranet char(1) NOT NULL default '0',
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY  (b_id)
) TYPE=MyISAM;

#
# �C�X�H�U��Ʈw���ƾڡG `board_p`
#

#
# ��ƪ��榡�G `school_day`
#

CREATE TABLE school_day (
  day_kind varchar(40) NOT NULL default '',
  day date NOT NULL default '0000-00-00',
  year tinyint(2) unsigned NOT NULL default '0',
  seme enum('1','2') NOT NULL default '1',
  UNIQUE KEY year_seme (day_kind,year,seme)
) TYPE=MyISAM;
#
# �C�X�H�U��Ʈw���ƾڡG `stud_absence`
#

ALTER TABLE `docup_p` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `docup_p` CHANGE `doc_kind_id` `doc_kind_id` INT DEFAULT '0' NOT NULL ;
ALTER TABLE `docup` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL ;
ALTER TABLE `score_setup` ADD `allow_modify` ENUM( 'false', 'true' ) NOT NULL ;


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

