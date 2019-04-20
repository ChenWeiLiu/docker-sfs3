# $Id: module.sql 8149 2014-09-27 02:32:17Z smallduh $

#
# 資料表格式： `csrc_record`
#

CREATE TABLE csrc_record (
  `id` int(10) unsigned NOT NULL auto_increment,
  `year` smallint(5) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  `main_inc` int(5) unsigned NOT NULL default '0',
  `sub_inc` int(5) unsigned NOT NULL default '0',
  `level` int(5) unsigned NOT NULL default '0',
  `inc_date` timestamp,
  `inc_place` int(5) unsigned NOT NULL default '0',
  `dper` int(5) unsigned NOT NULL default '0',
  `aper` int(5) unsigned NOT NULL default '0',
  `oper` int(5) unsigned NOT NULL default '0',
  `update_date` timestamp,
  PRIMARY KEY (id)
) ENGINE=MyISAM;

#
# 資料表格式： `csrc_item`
#

CREATE TABLE csrc_item (
  `main_id` int(5) unsigned NOT NULL default '0',
  `sub_id` int(5) unsigned NOT NULL default '0',
  `memo` text NOT NULL default '',
  PRIMARY KEY (main_id,sub_id)
) ENGINE=MyISAM;
