# $Id: module.sql 8152 2014-09-30 01:15:55Z smallduh $
CREATE TABLE stud_compile (
  compile_sn int(10) unsigned NOT NULL auto_increment,
  student_sn int(10) unsigned NOT NULL default '0',
  sort int unsigned NOT NULL default '0',
  old_class varchar(11) NOT NULL default '',
  new_class varchar(11) NOT NULL default '',
  site_num tinyint(3) unsigned NOT NULL default '0',
  sex tinyint(1) unsigned NOT NULL default '0',
  stud_birthday date NOT NULL default '0000-00-00',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  bs varchar(11) NOT NULL default '',
  PRIMARY KEY  (compile_sn)
) ENGINE=MyISAM;
