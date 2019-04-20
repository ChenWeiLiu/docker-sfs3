# $Id: module.sql 8150 2014-09-29 03:41:14Z smallduh $
# 資料表格式： `school_board`
#


CREATE TABLE school_board (
  userid varchar(25) NOT NULL default '',
  poster_name varchar(20) NOT NULL default '',
  poster_job varchar(20) NOT NULL default '',
  msg_id mediumint(10) NOT NULL auto_increment,
  msg_subject varchar(200) NOT NULL default '',
  msg_body text NOT NULL,
  msg_hit mediumint(10) NOT NULL default '0',
  msg_date datetime default NULL,
  attach varchar(80) NOT NULL default '',
  msg_date_expire datetime default NULL,
  inSchool tinyint(4) NOT NULL default '0',
  url varchar(80) default NULL,
  TopNews tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (msg_id),
  KEY userid (userid)
) ENGINE=MyISAM;
        