# $Id: module.sql 8150 2014-09-29 03:41:14Z smallduh $
#
# 資料表格式： `new_board`
#

CREATE TABLE new_board (
  serial int(10) unsigned NOT NULL auto_increment,
  title varchar(200) NOT NULL default '無主題',
  content text NOT NULL,
  teacher_sn smallint(5) unsigned NOT NULL default '1',
  post_date datetime NOT NULL default '0000-00-00 00:00:00',
  work_date date NOT NULL default '0000-00-00',
  FSN smallint(5) unsigned default NULL,
  image_url varchar(255) default NULL,
  PRIMARY KEY  (serial),
  KEY serial (serial)
) ENGINE=MyISAM;
