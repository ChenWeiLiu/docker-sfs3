# $Id: module.sql 8149 2014-09-27 02:32:17Z smallduh $

CREATE TABLE `copy_log` (
  `cp_sn` int(10) unsigned NOT NULL auto_increment,
  `sn` int(10) unsigned NOT NULL default '0',
  `tbl_name` varchar(255) NOT NULL default '',
  `date` datetime NOT NULL default '0000-00-00 00:00:00',
  `record` varchar(255) NOT NULL default '',
  `year` tinyint(3) unsigned NOT NULL default '0',
  `semester` enum('1','2') NOT NULL default '1',
  PRIMARY KEY  (`cp_sn`)
) ENGINE=MyISAM;
