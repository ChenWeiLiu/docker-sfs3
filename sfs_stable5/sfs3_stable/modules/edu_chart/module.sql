# $Id: module.sql 8149 2014-09-27 02:32:17Z smallduh $

#
# 資料表格式： `eyesight`
#

CREATE TABLE eyesight (
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  student_sn int(10) unsigned NOT NULL default '0',
  side varchar(1) NOT NULL default 'r',
  eyesight_value float(1,1) NOT NULL default '0.0',
  PRIMARY KEY (year,semester,student_sn,side)
) ENGINE=MyISAM;