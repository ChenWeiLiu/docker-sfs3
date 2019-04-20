# $Id: module.sql 5311 2009-01-10 08:11:55Z hami $
#
#
CREATE TABLE parent_link (
  link_sn bigint(20) NOT NULL auto_increment,
  author_sn varchar(21) NOT NULL default '',
  date date NOT NULL default '0000-00-00',
  time datetime NOT NULL default '0000-00-00 00:00:00',
  class_id varchar(11) default NULL,
  teacher_link bigint(20) NOT NULL default '0',
  parent_link varchar(200) NOT NULL default '0',
  content text NOT NULL,
  file_name varchar(200) default NULL,
  re_link bigint(20)  default NULL,
  PRIMARY KEY  (link_sn)
);

CREATE TABLE if not exists parent_auth (
  parent_sn int(11) NOT NULL auto_increment,
  parent_id varchar(10) NOT NULL default '',
  login_id varchar(20) NOT NULL default '',
  parent_pass varchar(20) NOT NULL default '',
  start_code varchar(10) NOT NULL default '',
  email varchar(60) NOT NULL default '',
  date datetime NOT NULL default '0000-00-00 00:00:00',
  enable tinyint(1) unsigned NOT NULL default '1',
  PRIMARY KEY parent_sn (parent_sn),
  UNIQUE KEY parent_id (parent_id)
);