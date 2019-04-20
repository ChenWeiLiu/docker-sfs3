# // $Id: module.sql 8687 2015-12-25 03:05:45Z qfon $
CREATE TABLE sign_data (
  id bigint(20) NOT NULL auto_increment,
  kind bigint(20) NOT NULL default '0',
  item varchar(10) NOT NULL default '',
  order_pos tinyint(4) NOT NULL default '0',
  stud_name varchar(20) NOT NULL default '',
  data_get tinytext,
  data_input tinytext NOT NULL,
  teach_id varchar(20) default NULL,
  class_id varchar(10) default NULL,
  PRIMARY KEY  (id),
  UNIQUE KEY myindex (class_id,kind,item,order_pos)
) ENGINE=MyISAM COMMENT='報名人資料表';


#
# 資料表格式： `sign_kind`
#

CREATE TABLE sign_kind (
  id bigint(20) NOT NULL auto_increment,
  title varchar(40) NOT NULL default '',
  doc text,
  beg_date date NOT NULL default '0000-00-00',
  end_date date NOT NULL default '0000-00-00',
  input_classY varchar(40) default '0',
  kind_set text,
  data_item varchar(200) NOT NULL default '',
  input_data_item varchar(200) default '0',
  admin varchar(15) default NULL,
  helper varchar(80) NOT NULL default '',
  is_hide tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (id)
) ENGINE=MyISAM COMMENT='報名單設計';


    
    
    ALTER TABLE `sign_kind` ADD `is_hide` TINYINT DEFAULT '0' NOT NULL ;
