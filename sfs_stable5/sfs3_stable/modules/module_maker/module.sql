# $Id: module.sql 8546 2015-10-01 01:38:43Z infodaes $

CREATE TABLE module_maker (
  mms smallint(5) unsigned NOT NULL auto_increment,
  author varchar(50) NOT NULL default '',
  email varchar(100) NOT NULL default '',
  creat_date datetime NOT NULL default '0000-00-00 00:00:00',
  lable varchar(50) NOT NULL default '',
  showname varchar(100) NOT NULL default '',
  dirname varchar(100) NOT NULL default '',
  index_page varchar(100) NOT NULL default '',
  description text NOT NULL,
  install text NOT NULL,
  news text NOT NULL,
  readme text NOT NULL,
  PRIMARY KEY  (mms)
) ;

CREATE TABLE module_maker_col (
  mmscs int(10) unsigned NOT NULL auto_increment,
  table_name varchar(100) NOT NULL default '',
  ename varchar(100) NOT NULL default '',
  cname varchar(100) NOT NULL default '',
  default_txt text NOT NULL,
  PRIMARY KEY  (mmscs),
  UNIQUE KEY table_name (table_name,ename)
) ;