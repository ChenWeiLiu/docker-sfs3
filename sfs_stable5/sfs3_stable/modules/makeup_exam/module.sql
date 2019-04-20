# $Id:$
#
# 資料表格式： `makeup_exam_score`
#

CREATE TABLE makeup_exam_score (
  id int(10) unsigned NOT NULL auto_increment,
  seme_year_seme varchar(6) NOT NULL default '',
  student_sn int(10) unsigned NOT NULL default '0',
  scope_ename varchar(200) NOT NULL default '',
  ss_id smallint(5) unsigned NOT NULL default '0',
  class_year tinyint(4) unsigned NOT NULL default '0',
  oscore float unsigned NOT NULL default '0',
  nscore float unsigned NOT NULL default '0',
  rate int(3) unsigned NOT NULL default '0', 
  test varchar(1) NOT NULL default '0',
  chg varchar(1) NOT NULL default '0',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  teacher_sn smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY (seme_year_seme,student_sn,ss_id),
  KEY(id)
) ENGINE=MyISAM;

#
# 資料表格式： `makeup_exam_scope`
#

CREATE TABLE makeup_exam_scope (
  id int(10) unsigned NOT NULL auto_increment,
  seme_year_seme varchar(6) NOT NULL default '',
  student_sn int(10) unsigned NOT NULL default '0',
  scope_ename varchar(200) NOT NULL default '',
  class_year tinyint(4) unsigned NOT NULL default '0',
  oscore float unsigned NOT NULL default '0',
  nscore float unsigned NOT NULL default '0',
  has_score varchar(1) NOT NULL default '0',
  act varchar(1) NOT NULL default '0',
  update_time datetime NOT NULL default '0000-00-00 00:00:00',
  teacher_sn smallint(6) unsigned NOT NULL default '0',
  PRIMARY KEY (seme_year_seme,student_sn,scope_ename),
  KEY(id)
) ENGINE=MyISAM;
