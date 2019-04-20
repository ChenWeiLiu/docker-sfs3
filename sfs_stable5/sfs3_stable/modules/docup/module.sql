CREATE TABLE docup (
  docup_p_id int(11) NOT NULL default '0',
  docup_id int(11) NOT NULL auto_increment,
  docup_name varchar(80) NOT NULL default '',
  docup_date datetime NOT NULL default '0000-00-00 00:00:00',
  docup_owner varchar(12) NOT NULL default '',
  docup_store varchar(80) NOT NULL default '',
  docup_share char(3) NOT NULL default '0',
  docup_owerid varchar(6) NOT NULL default '',
  teacher_sn int(11) NOT NULL default '0',
  docup_file_size int(11) NOT NULL default '0',
  PRIMARY KEY  (docup_id)
) ENGINE=MyISAM;
# --------------------------------------------------------

#
# 資料表格式： `docup_p`
#

CREATE TABLE docup_p (
  doc_kind_id int(11) NOT NULL default '0',
  docup_p_id int(11) NOT NULL auto_increment,
  docup_p_date datetime NOT NULL default '0000-00-00 00:00:00',
  docup_p_name varchar(60) default NULL,
  docup_p_memo text,
  docup_p_owner varchar(12) default NULL,
  docup_p_ownerid varchar(6) NOT NULL default '',
  docup_p_count int(11) NOT NULL default '0',
  teacher_sn int(11) NOT NULL default '0',
  PRIMARY KEY  (docup_p_id)
) ENGINE=MyISAM;


