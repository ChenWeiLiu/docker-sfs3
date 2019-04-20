# $Id: module.sql 8149 2014-09-27 02:32:17Z smallduh $
#
# 資料表格式： `calendar`
#
                                                                                                                             
CREATE TABLE calendar (
  cal_sn smallint(5) unsigned NOT NULL auto_increment,
  year smallint(4) unsigned NOT NULL default '0',
  month tinyint(2) unsigned NOT NULL default '0',
  day tinyint(2) unsigned NOT NULL default '0',
  week enum('0','1','2','3','4','5','6') NOT NULL default '0',
  time time NOT NULL default '00:00:00',
  place varchar(255) NOT NULL default '',
  thing text NOT NULL,
  kind varchar(255) NOT NULL default '',
  teacher_sn smallint(5) unsigned NOT NULL default '0',
  from_teacher_sn smallint(5) unsigned NOT NULL default '0',
  from_cal_sn mediumint(8) unsigned NOT NULL default '0',
  restart enum('0','md','d','w') NOT NULL default '0',
  restart_day date NOT NULL default '0000-00-00',
  restart_end date NOT NULL default '0000-00-00',
  import varchar(255) NOT NULL default '',
  post_time datetime NOT NULL default '0000-00-00 00:00:00',
  PRIMARY KEY  (cal_sn),
  KEY time (time),
  KEY teacher_sn (teacher_sn),
  KEY from_cal_sn (from_cal_sn),
  KEY week (week),
  KEY restart_day (restart_day,restart_end)
) ENGINE=MyISAM;

