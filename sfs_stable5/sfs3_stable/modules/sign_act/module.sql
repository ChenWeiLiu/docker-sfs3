# // $Id: module.sql 8546 2015-10-01 01:38:43Z infodaes $
# 資料表格式： `sign_act_kind`
#
CREATE TABLE sign_act_kind (
  id int(11) NOT NULL auto_increment,
  act_name varchar(60) NOT NULL default '',
  act_doc varchar(200) NOT NULL default '',
  act_passwd varchar(20) NOT NULL default '',
  beg_date date NOT NULL default '0000-00-00',
  end_date date NOT NULL default '0000-00-00',
  team_set tinytext NOT NULL,
  max_team tinyint(4) NOT NULL default '0',
  max_each tinyint(4) NOT NULL default '1',
  member_set tinytext,
  fields_set tinytext,
  admin varchar(20) default NULL,
  manager varchar(40) default NULL,
  school_list text NOT NULL,
  PRIMARY KEY  (id)
) ENGINE=MyISAM;
    

    

#
# 資料表格式： `sign_act_data`
#

CREATE TABLE sign_act_data (
  did bigint(20) NOT NULL auto_increment,
  pid int(11) NOT NULL default '0',
  school_name varchar(20) NOT NULL default '',
  team_id varchar(20) default NULL,
  set_passwd varchar(20) default NULL,
  data text,
  PRIMARY KEY  (did),
  KEY pid (pid,school_name,team_id)
) ;

    