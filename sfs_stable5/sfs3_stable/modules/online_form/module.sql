# $Id: module.sql 8546 2015-10-01 01:38:43Z infodaes $

#
# 資料表格式： `form_all`
#

CREATE TABLE form_all (
  ofsn smallint(5) unsigned NOT NULL auto_increment,
  of_title varchar(255) NOT NULL default '',
  of_start_date date NOT NULL default '0000-00-00',
  of_dead_line date NOT NULL default '0000-00-00',
  of_text text,
  of_who varchar(255) default NULL,
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  of_communication varchar(255) default NULL,
  of_date datetime NOT NULL default '0000-00-00 00:00:00',
  enable enum('1','0') NOT NULL default '1',
  PRIMARY KEY  (ofsn),
  KEY eduer_unit_sn (teacher_sn)
) ;
# --------------------------------------------------------

#
# 資料表格式： `form_col`
#

CREATE TABLE form_col (
  col_sn int(10) unsigned NOT NULL auto_increment,
  ofsn smallint(5) unsigned NOT NULL default '0',
  col_title varchar(255) NOT NULL default '',
  col_text text,
  col_dataType enum('date','varchar','int','bool') NOT NULL default 'date',
  col_value varchar(255) default NULL,
  col_chk enum('1','0') default NULL,
  col_function set('sum','avg','count') default NULL,
  col_sort tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (col_sn),
  KEY ofsn (ofsn)
) ;
# --------------------------------------------------------

#
# 資料表格式： `form_fill_in`
#

CREATE TABLE form_fill_in (
  schfi_sn int(10) unsigned NOT NULL auto_increment,
  ofsn smallint(5) unsigned NOT NULL default '0',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  man_name varchar(20) NOT NULL default '',
  tel varchar(10) NOT NULL default '',
  email varchar(50) NOT NULL default '',
  fill_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (schfi_sn),
  KEY ofsn (ofsn,teacher_sn),
  KEY SHN (teacher_sn)
) ;
# --------------------------------------------------------

#
# 資料表格式： `form_value`
#

CREATE TABLE form_value (
  value_sn bigint(20) unsigned NOT NULL auto_increment,
  schfi_sn int(10) unsigned NOT NULL default '0',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  ofsn smallint(5) unsigned NOT NULL default '0',
  col_sn int(10) unsigned NOT NULL default '0',
  value varchar(255) NOT NULL default '',
  PRIMARY KEY  (value_sn),
  KEY schfi_sn (schfi_sn,ofsn,col_sn),
  KEY SHN (teacher_sn),
  KEY col_sn (col_sn)
) ;
