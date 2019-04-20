# $Id: course_room.sql 8150 2014-09-29 03:41:14Z smallduh $

# phpMyAdmin MySQL-Dump
# version 2.3.3pl1
# http://www.phpmyadmin.net/ (download page)
#
# 主機: localhost
# 建立日期: Feb 11, 2003 at 08:00 PM
# 伺服器版本: 3.23.54
# PHP 版本: 4.2.3
# 資料庫: `sfs3`
# --------------------------------------------------------

#
# 資料表格式： `course_room`
#

CREATE TABLE course_room (
  crsn int(10) unsigned NOT NULL auto_increment,
  date date NOT NULL default '0000-00-00',
  day enum('0','1','2','3','4','5','6','7') NOT NULL default '0',
  sector tinyint(1) unsigned NOT NULL default '0',
  room varchar(50) NOT NULL default '',
  teacher_sn mediumint(8) unsigned NOT NULL default '0',
  sign_date datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (crsn),
  KEY teacher_sn (teacher_sn),
  KEY sector (sector),
  KEY day (day)
) ENGINE=MyISAM COMMENT='      ';



# 資料表格式： `spec_classroom`
#

CREATE TABLE spec_classroom (
  room_id smallint(5) unsigned NOT NULL auto_increment,
  room_name varchar(20) NOT NULL default '',
  enable enum('0','1') NOT NULL default '1',
  notfree_time varchar(250) default NULL,
  PRIMARY KEY  (room_id)
) ENGINE=MyISAM;