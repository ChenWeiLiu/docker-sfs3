
# $Id: module.sql 8151 2014-09-29 03:41:24Z smallduh $
#
# 資料表格式： `reward`
#

CREATE TABLE reward (
  reward_div varchar(4) NOT NULL default '0',
  reward_id bigint(20) NOT NULL auto_increment,
  stud_id varchar(20) NOT NULL default '',
  reward_kind varchar(10) NOT NULL default '',
  reward_year_seme varchar(6) NOT NULL default '',
  reward_date date NOT NULL default '0000-00-00',
  reward_reason text,
  reward_c_date date default '0000-00-00',
  reward_base text,
  reward_cancel_date date NOT NULL default '0000-00-00',
  update_id varchar(20) NOT NULL default '',
  update_ip varchar(15) NOT NULL default '',
  reward_sub tinyint(4) NOT NULL default '0',
  dep_id tinyint(4) NOT NULL default '0',
  student_sn int(10) default '0',
  PRIMARY KEY  (reward_id)
) ENGINE=MyISAM;
