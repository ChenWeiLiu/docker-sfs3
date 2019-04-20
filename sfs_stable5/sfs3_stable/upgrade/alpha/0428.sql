#
# 資料表格式： `board_check`
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
# 列出以下資料庫的數據： `board_check`
#

INSERT INTO board_check VALUES (1, 'office1', 0, 'none', 0, '', 1);
INSERT INTO board_check VALUES (2, 'office2', 0, 'none', 0, '', 1);
# --------------------------------------------------------

#
# 資料表格式： `board_kind`
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
# 列出以下資料庫的數據： `board_kind`
#

INSERT INTO board_kind VALUES ('office1', '教務處', '2003-04-28', '0', '0000-00-00', '1', '1', '');
INSERT INTO board_kind VALUES ('office2', '訓導處', '2003-04-28', '0', '0000-00-00', '1', '1', '');
# --------------------------------------------------------

#
# 資料表格式： `board_p`
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
# 列出以下資料庫的數據： `board_p`
#


ALTER TABLE `docup_p` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL AUTO_INCREMENT;
ALTER TABLE `docup_p` CHANGE `doc_kind_id` `doc_kind_id` INT DEFAULT '0' NOT NULL ;
ALTER TABLE `docup` CHANGE `docup_p_id` `docup_p_id` INT NOT NULL ;

