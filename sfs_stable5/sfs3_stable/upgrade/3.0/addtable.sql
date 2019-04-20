# $Id: addtable.sql 5311 2009-01-10 08:11:55Z hami $
# phpMyAdmin MySQL-Dump
# version 2.2.3
# http://phpwizard.net/phpMyAdmin/
# http://phpmyadmin.sourceforge.net/ (download page)
#


#
# Table structure for table `sys_data_field`
#

CREATE TABLE sys_data_field (
  d_table_name varchar(30) NOT NULL default '',
  d_field_name varchar(30) NOT NULL default '',
  d_field_cname  varchar(30) NOT NULL default '',
  d_field_type varchar(30) NOT NULL default '',
  d_field_order  tinyint(4) NOT NULL default '0',
  d_is_display tinyint(4) NOT NULL default '0',
  d_field_xml varchar(40) NOT NULL default '',
  PRIMARY KEY  (d_table_name,d_field_name),
  UNIQUE KEY d_table_name (d_table_name,d_field_name)
) TYPE=MyISAM COMMENT='資料欄位';
# --------------------------------------------------------

#
# Table structure for table `sys_data_table`
#

CREATE TABLE sys_data_table (
  d_table_name varchar(30) NOT NULL default '',
  d_table_cname varchar(30) NOT NULL default '',
  d_table_group varchar(30) NOT NULL default '',
  PRIMARY KEY  (d_table_name),
  UNIQUE KEY d_table_name (d_table_name)
) TYPE=MyISAM COMMENT='資料庫名稱';

 
