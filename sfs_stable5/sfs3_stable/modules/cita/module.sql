# phpMyAdmin MySQL-Dump
# version 2.4.0
# http://www.phpmyadmin.net/ (download page)
#
# 主機: localhost
# 建立日期: Mar 01, 2005 at 09:58 PM
# 伺服器版本: 3.23.54
# PHP 版本: 4.2.2
# 資料庫: `sfs3`
# --------------------------------------------------------

#
# 資料表格式： `cita_data`
#

CREATE TABLE IF NOT EXISTS `cita_data` (
  `id` bigint(20) NOT NULL auto_increment,
  `kind` bigint(20) NOT NULL default '0',
  `num` varchar(10) NOT NULL default '',
  `order_pos` tinyint(4) NOT NULL default '0',
  `stud_name` varchar(20) NOT NULL default '',
  `data_get` tinytext,
  `data_input` tinytext NOT NULL,
  `teach_id` varchar(20) default NULL,
  `class_id` varchar(20) default NULL,
  `stud_id` varchar(20) NOT NULL default '',
  `up_date` date NOT NULL default '0000-00-00',
  PRIMARY KEY  (`id`)
);
# --------------------------------------------------------

#
# 資料表格式： `cita_kind`
#

CREATE TABLE IF NOT EXISTS `cita_kind` (
  `id` bigint(20) NOT NULL auto_increment,
  `title` varchar(80) NOT NULL default '',
  `doc` text,
  `beg_date` date NOT NULL default '0000-00-00',
  `end_date` date NOT NULL default '0000-00-00',
  `input_classY` varchar(40) default '0',
  `kind_set` text,
  `helper` text NOT NULL,
  `input_data_item` varchar(200) default '0',
  `admin` varchar(15) default NULL,
  `foot` varchar(80) NOT NULL default '',
  `is_hide` tinyint(4) NOT NULL default '0',
  `grada` tinyint(1) NOT NULL default '0',
  `max` tinyint(2) NOT NULL default '18',
  PRIMARY KEY  (`id`)
);

