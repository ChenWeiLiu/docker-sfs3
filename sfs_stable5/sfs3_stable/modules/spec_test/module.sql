# $Id: module.sql 8152 2014-09-30 01:15:55Z smallduh $
#
# 資料表格式： `test_manage`
#

CREATE TABLE test_manage (
  id int(10) unsigned NOT NULL auto_increment,
  year smallint(5) unsigned NOT NULL default '0',
  semester enum('1','2') NOT NULL default '1',
  c_year tinyint(2) unsigned NOT NULL default '0',
  title text NOT NULL,
  subject_str text NOT NULL,
  ratio_str text NOT NULL,
  compare_id int(10) unsigned NOT NULL default '0',
  PRIMARY KEY (id),
  KEY serial (id)
) ENGINE=MyISAM;
